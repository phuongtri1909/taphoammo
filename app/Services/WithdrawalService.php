<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Models\WalletTransaction;
use App\Enums\WithdrawalStatus;
use App\Enums\WalletStatus;
use App\Enums\WalletTransactionType;
use App\Enums\WalletTransactionStatus;
use App\Enums\WalletTransactionReferenceType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class WithdrawalService
{
    const OTP_EXPIRY_MINUTES = 10;
    const MIN_WITHDRAWAL_AMOUNT = 50000;
    const RESEND_OTP_COOLDOWN_MINUTES = 3;

    public function createWithdrawal(
        User $user,
        int $amount,
        string $bankName,
        string $bankAccountNumber,
        string $bankAccountName,
        ?string $note = null
    ): Withdrawal {
        if ($user->role !== 'seller') {
            throw new \Exception('Chỉ seller mới được rút tiền');
        }

        $this->validateAmount($user, $amount);

        $otpCode = $this->generateOtp();
        $otpExpiresAt = now()->addMinutes(self::OTP_EXPIRY_MINUTES);

        $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'bank_name' => $bankName,
            'bank_account_number' => $bankAccountNumber,
            'bank_account_name' => $bankAccountName,
            'status' => WithdrawalStatus::PENDING_OTP,
            'otp_code' => $otpCode,
            'otp_expires_at' => $otpExpiresAt,
            'note' => $note,
        ]);

        $this->sendOtpEmail($user, $otpCode, $withdrawal);

        return $withdrawal;
    }

    protected function validateAmount(User $user, int $amount): void
    {
        if ($amount < self::MIN_WITHDRAWAL_AMOUNT) {
            throw new \InvalidArgumentException('Số tiền tối thiểu là ' . number_format(self::MIN_WITHDRAWAL_AMOUNT, 0, ',', '.') . '₫');
        }

        if ($amount % 10000 !== 0) {
            throw new \InvalidArgumentException('Số tiền phải là bội số của 10,000₫');
        }

        $wallet = Wallet::where('user_id', $user->id)->first();
        if (!$wallet || $wallet->balance < $amount) {
            throw new \InvalidArgumentException('Số dư không đủ');
        }
    }

    protected function generateOtp(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    protected function sendOtpEmail(User $user, string $otpCode, Withdrawal $withdrawal): void
    {
        try {
            Mail::send('emails.withdrawal-otp', [
                'user' => $user,
                'otpCode' => $otpCode,
                'withdrawal' => $withdrawal,
                'expiryMinutes' => self::OTP_EXPIRY_MINUTES,
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Xác nhận rút tiền - Mã OTP');
            });
        } catch (\Exception $e) {
            Log::error('Failed to send withdrawal OTP email: ' . $e->getMessage());
        }
    }

    public function verifyOtp(Withdrawal $withdrawal, string $otp): bool
    {
        if ($withdrawal->status !== WithdrawalStatus::PENDING_OTP) {
            throw new \Exception('Yêu cầu rút tiền không ở trạng thái chờ xác thực');
        }

        if (!$withdrawal->isOtpValid($otp)) {
            throw new \Exception('Mã OTP không đúng hoặc đã hết hạn');
        }

        return DB::transaction(function () use ($withdrawal) {
            $wallet = Wallet::where('user_id', $withdrawal->user_id)
                ->lockForUpdate()
                ->first();

            if (!$wallet || $wallet->balance < $withdrawal->amount) {
                throw new \Exception('Số dư không đủ để rút tiền');
            }

            $balanceBefore = $wallet->balance;
            $balanceAfter = $balanceBefore - $withdrawal->amount;
            $wallet->update(['balance' => $balanceAfter]);

            $withdrawal->update([
                'status' => WithdrawalStatus::PENDING,
                'otp_verified' => true,
            ]);

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => WalletTransactionType::WITHDRAW->value,
                'amount' => $withdrawal->amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reference_type' => WalletTransactionReferenceType::WITHDRAWAL->value,
                'reference_id' => $withdrawal->id,
                'description' => "Yêu cầu rút tiền #{$withdrawal->slug}",
                'status' => WalletTransactionStatus::COMPLETED->value,
            ]);

            try {
                $telegramService = new \App\Services\TelegramNotificationService();
                $withdrawal->load('user');
                $telegramService->sendWithdrawalNotification($withdrawal->fresh());
            } catch (\Exception $e) {
                Log::warning('Không thể gửi thông báo Telegram cho yêu cầu rút tiền', [
                    'withdrawal_id' => $withdrawal->id,
                    'error' => $e->getMessage()
                ]);
            }

            return true;
        });
    }

    public function resendOtp(Withdrawal $withdrawal): void
    {
        if ($withdrawal->status !== WithdrawalStatus::PENDING_OTP) {
            throw new \Exception('Không thể gửi lại OTP cho yêu cầu này');
        }

        $cacheKey = 'withdrawal_resend_otp_cooldown_' . $withdrawal->id;
        $lastSentTime = Cache::get($cacheKey);

        if ($lastSentTime) {
            $cooldownEndTime = (clone $lastSentTime)->addMinutes(self::RESEND_OTP_COOLDOWN_MINUTES);
            
            if (now()->lt($cooldownEndTime)) {
                $secondsLeft = now()->diffInSeconds($cooldownEndTime, false);
                
                if ($secondsLeft > 0) {
                    $minutesLeft = (int) ceil($secondsLeft / 60);
                    throw new \Exception("Vui lòng đợi {$minutesLeft} phút nữa trước khi gửi lại OTP.");
                }
            }
        }

        $cooldownExpiresAt = now()->addMinutes(self::RESEND_OTP_COOLDOWN_MINUTES + 1);
        Cache::put($cacheKey, now(), $cooldownExpiresAt);

        $otpCode = $this->generateOtp();
        $otpExpiresAt = now()->addMinutes(self::OTP_EXPIRY_MINUTES);

        $withdrawal->update([
            'otp_code' => $otpCode,
            'otp_expires_at' => $otpExpiresAt,
        ]);

        $this->sendOtpEmail($withdrawal->user, $otpCode, $withdrawal);
    }

    public function cancelWithdrawal(Withdrawal $withdrawal): void
    {
        if (!in_array($withdrawal->status, [WithdrawalStatus::PENDING_OTP, WithdrawalStatus::PENDING])) {
            throw new \Exception('Không thể hủy yêu cầu rút tiền này');
        }

        DB::transaction(function () use ($withdrawal) {
            if ($withdrawal->status === WithdrawalStatus::PENDING) {
                $wallet = Wallet::where('user_id', $withdrawal->user_id)
                    ->lockForUpdate()
                    ->first();

                $balanceBefore = $wallet->balance;
                $balanceAfter = $balanceBefore + $withdrawal->amount;
                $wallet->update(['balance' => $balanceAfter]);

                WalletTransaction::where('reference_type', WalletTransactionReferenceType::WITHDRAWAL->value)
                    ->where('reference_id', $withdrawal->id)
                    ->update([
                        'status' => WalletTransactionStatus::FAILED->value,
                        'description' => "Yêu cầu rút tiền #{$withdrawal->slug} đã hủy",
                    ]);

                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => WalletTransactionType::REFUND->value,
                    'amount' => $withdrawal->amount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'reference_type' => WalletTransactionReferenceType::WITHDRAWAL->value,
                    'reference_id' => $withdrawal->id,
                    'description' => "Hoàn tiền do hủy rút tiền #{$withdrawal->slug}",
                    'status' => WalletTransactionStatus::COMPLETED->value,
                ]);
            }

            $withdrawal->update(['status' => WithdrawalStatus::CANCELLED]);
        });
    }

    public function markProcessing(Withdrawal $withdrawal, int $adminId): void
    {
        if ($withdrawal->status !== WithdrawalStatus::PENDING) {
            throw new \Exception('Yêu cầu không ở trạng thái chờ xử lý');
        }

        $withdrawal->update([
            'status' => WithdrawalStatus::PROCESSING,
            'processed_by' => $adminId,
        ]);
    }

    public function complete(Withdrawal $withdrawal, int $adminId, ?string $adminNote = null): void
    {
        if (!in_array($withdrawal->status, [WithdrawalStatus::PENDING, WithdrawalStatus::PROCESSING])) {
            throw new \Exception('Yêu cầu không ở trạng thái có thể hoàn thành');
        }

        $withdrawal->update([
            'status' => WithdrawalStatus::COMPLETED,
            'processed_by' => $adminId,
            'processed_at' => now(),
            'admin_note' => $adminNote,
        ]);
    }

    public function reject(Withdrawal $withdrawal, int $adminId, string $adminNote): void
    {
        if (!in_array($withdrawal->status, [WithdrawalStatus::PENDING, WithdrawalStatus::PROCESSING])) {
            throw new \Exception('Yêu cầu không ở trạng thái có thể từ chối');
        }

        DB::transaction(function () use ($withdrawal, $adminId, $adminNote) {
            $wallet = Wallet::where('user_id', $withdrawal->user_id)
                ->lockForUpdate()
                ->first();

            $balanceBefore = $wallet->balance;
            $balanceAfter = $balanceBefore + $withdrawal->amount;
            $wallet->update(['balance' => $balanceAfter]);

            $withdrawal->update([
                'status' => WithdrawalStatus::REJECTED,
                'processed_by' => $adminId,
                'processed_at' => now(),
                'admin_note' => $adminNote,
            ]);

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => WalletTransactionType::REFUND->value,
                'amount' => $withdrawal->amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reference_type' => WalletTransactionReferenceType::WITHDRAWAL->value,
                'reference_id' => $withdrawal->id,
                'description' => "Hoàn tiền do từ chối rút tiền #{$withdrawal->slug}",
                'status' => WalletTransactionStatus::COMPLETED->value,
            ]);
        });
    }

    public function cancelExpiredOtpRequests(): int
    {
        $expired = Withdrawal::where('status', WithdrawalStatus::PENDING_OTP)
            ->where('otp_expires_at', '<', now())
            ->get();

        $count = 0;
        foreach ($expired as $withdrawal) {
            try {
                $this->cancelWithdrawal($withdrawal);
                $count++;
            } catch (\Exception $e) {
                Log::error('Lỗi hủy yêu cầu rút tiền hết hạn: ' . $e->getMessage());
            }
        }

        return $count;
    }
}
