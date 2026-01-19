<?php

namespace App\Services;

use App\Models\Bank;
use App\Models\Deposit;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Enums\DepositStatus;
use App\Enums\WalletStatus;
use App\Enums\WalletTransactionType;
use App\Enums\WalletTransactionStatus;
use App\Enums\WalletTransactionReferenceType;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class DepositService
{
    public function createDeposit(int $userId, int $bankId, int $amount): Deposit
    {
        $this->validateAmount($amount);

        $bank = Bank::where('id', $bankId)->where('status', true)->firstOrFail();

        $transactionCode = $this->generateUniqueTransactionCode();

        return Deposit::create([
            'user_id' => $userId,
            'bank_id' => $bankId,
            'bank_name' => $bank->name,
            'bank_code' => $bank->code,
            'bank_account_number' => $bank->account_number,
            'bank_account_name' => $bank->account_name,
            'transaction_code' => $transactionCode,
            'amount' => $amount,
            'status' => DepositStatus::PENDING,
        ]);
    }

    public function validateAmount(int $amount): void
    {
        if ($amount < 10000) {
            throw new \InvalidArgumentException('Số tiền tối thiểu là 10,000₫');
        }

        if ($amount % 10000 !== 0) {
            throw new \InvalidArgumentException('Số tiền phải là bội số của 10,000₫');
        }

        if ($amount > 100000000) {
            throw new \InvalidArgumentException('Số tiền tối đa là 100,000,000₫');
        }
    }

    protected function generateUniqueTransactionCode(): string
    {
        $maxAttempts = 100;
        $attempts = 0;

        do {
            $randomPart = strtoupper(Str::random(7));
            $transactionCode = 'DPMMO' . $randomPart;
            
            $exists = Deposit::where('transaction_code', $transactionCode)->exists();
            
            if (!$exists) {
                return $transactionCode;
            }
            
            $attempts++;
            
            if ($attempts >= $maxAttempts) {
                throw new \RuntimeException('Không thể tạo mã giao dịch duy nhất sau ' . $maxAttempts . ' lần thử');
            }
        } while (true);
    }

    public function getBankInfoWithQR(Bank $bank, string $transactionCode, int $amount): array
    {
        $qrCodeData = $this->generateVietQR($bank, $transactionCode, $amount);

        return [
            'id' => $bank->id,
            'name' => $bank->name,
            'code' => $bank->code,
            'account_number' => $bank->account_number,
            'account_name' => $bank->account_name,
            'qr_code' => $qrCodeData,
        ];
    }

    protected function generateVietQR(Bank $bank, string $transactionCode, int $amount): ?string
    {
        try {
            $url = "https://img.vietqr.io/image/{$bank->code}-{$bank->account_number}-compact2.jpg";

            $params = [
                'amount' => $amount,
                'addInfo' => $transactionCode,
                'accountName' => $bank->account_name
            ];

            $fullUrl = $url . '?' . http_build_query($params);

            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::timeout(10)->get($fullUrl);

            if ($response->ok()) {
                return 'data:image/jpeg;base64,' . base64_encode((string) $response->getBody());
            }

            return null;
        } catch (\Exception $e) {
            Log::error('VietQR API Exception: ' . $e->getMessage());
            return null;
        }
    }

    public function processCallback(array $data): array
    {
        $transactionId = $data['data']['id'] ?? null;
        $description = $data['data']['description'] ?? '';
        $amount = $data['data']['amount'] ?? 0;

        if (!$transactionId) {
            return ['success' => false, 'message' => 'transaction id không tồn tại'];
        }

        $existingDeposit = Deposit::where('casso_transaction_id', $transactionId)
            ->where('status', DepositStatus::SUCCESS)
            ->first();

        if ($existingDeposit) {
            return ['success' => true, 'message' => 'giao dịch đã được xử lý'];
        }

        $transactionCode = $this->extractTransactionCode($description);

        if (!$transactionCode) {
            Log::warning('Không thể lấy mã giao dịch từ mô tả', ['description' => $description]);
            return ['success' => false, 'message' => 'Không thể lấy mã giao dịch từ mô tả'];
        }

        $deposit = Deposit::where('transaction_code', $transactionCode)
            ->where('status', DepositStatus::PENDING)
            ->first();

        if (!$deposit) {
            Log::warning('giao dịch không tồn tại', [
                'transaction_code' => $transactionCode,
                'description' => $description
            ]);
            return ['success' => false, 'message' => 'giao dịch không tồn tại'];
        }

        return DB::transaction(function () use ($deposit, $amount, $transactionId, $data) {
            $toleranceAmount = $deposit->amount;
            if ($amount < $toleranceAmount) {
                $deposit->update([
                    'status' => DepositStatus::FAILED,
                    'note' => 'Số tiền nhận được không đủ. Cần: ' . $deposit->amount . ', nhận: ' . $amount,
                    'casso_response' => $data,
                    'casso_transaction_id' => $transactionId,
                ]);

                return ['success' => false, 'message' => 'Số tiền nhận được không đủ. Cần: ' . $deposit->amount . ', nhận: ' . $amount];
            }

            $deposit->update([
                'status' => DepositStatus::SUCCESS,
                'amount_received' => $amount,
                'processed_at' => now(),
                'casso_transaction_id' => $transactionId,
                'casso_response' => $data,
            ]);

            $this->addToWallet($deposit);

            $this->broadcastUpdate($deposit);

            return ['success' => true, 'message' => 'giao dịch đã được xử lý'];
        });
    }

    protected function extractTransactionCode(string $description): ?string
    {
        if (preg_match('/(DPMMO[A-Z0-9]{7})/', $description, $matches)) {
            return $matches[1];
        }

        return null;
    }

    protected function addToWallet(Deposit $deposit): void
    {
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $deposit->user_id],
            ['balance' => 0, 'status' => WalletStatus::ACTIVE]
        );

        $wallet = Wallet::where('id', $wallet->id)
            ->lockForUpdate()
            ->first();

        $before = $wallet->balance;
        $after = $before + $deposit->amount_received;

        $wallet->update(['balance' => $after]);

        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => WalletTransactionType::DEPOSIT->value,
            'amount' => $deposit->amount_received,
            'balance_before' => $before,
            'balance_after' => $after,
            'reference_type' => WalletTransactionReferenceType::DEPOSIT->value,
            'reference_id' => $deposit->id,
            'description' => "Nạp tiền qua ngân hàng #{$deposit->transaction_code}",
            'status' => WalletTransactionStatus::COMPLETED->value,
        ]);
    }

    /**
     * Broadcast SSE update
     */
    protected function broadcastUpdate(Deposit $deposit): void
    {
        $sseData = [
            'transaction_code' => $deposit->transaction_code,
            'status' => 'success',
            'deposit_id' => $deposit->id,
            'amount' => $deposit->amount,
            'amount_received' => $deposit->amount_received,
            'timestamp' => now()->toISOString(),
        ];

        $sseDir = storage_path('app/sse_transactions');
        if (!file_exists($sseDir)) {
            mkdir($sseDir, 0755, true);
        }

        $filename = $sseDir . '/sse_transaction_' . $deposit->transaction_code . '.json';
        file_put_contents($filename, json_encode($sseData));
    }

    public function verifyCassoSignature(string $payload, string $signature): bool
    {
        $secret = config('services.casso.webhook_secret');

        if (!$secret) {
            Log::error('Casso webhook secret not configured');
            return false;
        }

        if (!preg_match('/t=(\d+),v1=(.+)/', $signature, $matches)) {
            return false;
        }

        $timestamp = $matches[1];
        $receivedSignature = $matches[2];

        $currentTime = time() * 1000;
        $signatureTime = (int) $timestamp;
        if (abs($currentTime - $signatureTime) > 300000) {
            return false;
        }

        $data = json_decode($payload, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        $sortedData = $this->sortDataByKey($data);
        $messageToSign = $timestamp . '.' . json_encode($sortedData, JSON_UNESCAPED_SLASHES);
        $expectedSignature = hash_hmac('sha512', $messageToSign, $secret);

        return hash_equals($expectedSignature, $receivedSignature);
    }

    protected function sortDataByKey($data): mixed
    {
        if (!is_array($data)) {
            return $data;
        }

        $sortedData = [];
        $keys = array_keys($data);
        sort($keys);

        foreach ($keys as $key) {
            $sortedData[$key] = is_array($data[$key]) ? $this->sortDataByKey($data[$key]) : $data[$key];
        }

        return $sortedData;
    }

    public function cancelExpiredDeposits(int $minutesOld = 30): int
    {
        return Deposit::where('status', DepositStatus::PENDING)
            ->where('created_at', '<', now()->subMinutes($minutesOld))
            ->update(['status' => DepositStatus::EXPIRED]);
    }
}
