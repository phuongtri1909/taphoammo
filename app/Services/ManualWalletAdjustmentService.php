<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\ManualWalletAdjustment;
use App\Models\WalletTransaction;
use App\Enums\ManualAdjustmentType;
use App\Enums\WalletTransactionType;
use App\Enums\WalletTransactionReferenceType;
use App\Enums\WalletTransactionStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ManualWalletAdjustmentService
{
    public function createAdjustment(
        int $userId,
        ManualAdjustmentType $type,
        float $amount,
        string $reason,
        ?string $adminNote,
        int $adminId
    ): ManualWalletAdjustment {
        if ($amount <= 0) {
            throw new \DomainException('Số tiền phải lớn hơn 0');
        }

        return DB::transaction(function () use ($userId, $type, $amount, $reason, $adminNote, $adminId) {
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $userId],
                ['balance' => 0, 'status' => \App\Enums\WalletStatus::ACTIVE]
            );

            $wallet = Wallet::where('id', $wallet->id)
                ->lockForUpdate()
                ->first();

            if ($type === ManualAdjustmentType::SUBTRACT && $wallet->balance < $amount) {
                throw new \DomainException(
                    "Số dư không đủ để trừ. Số dư hiện tại: " . number_format($wallet->balance, 0, ',', '.') . "đ"
                );
            }

            $balanceBefore = $wallet->balance;
            $balanceAfter = $type === ManualAdjustmentType::ADD
                ? $balanceBefore + $amount
                : $balanceBefore - $amount;

            $adjustment = ManualWalletAdjustment::create([
                'user_id' => $userId,
                'adjustment_type' => $type,
                'amount' => $amount,
                'reason' => $reason,
                'admin_note' => $adminNote,
                'processed_by' => $adminId,
                'processed_at' => now(),
            ]);

            $wallet->update(['balance' => $balanceAfter]);

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => WalletTransactionType::MANUAL_ADJUSTMENT,
                'amount' => $type === ManualAdjustmentType::ADD ? $amount : -$amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reference_type' => WalletTransactionReferenceType::MANUAL_ADJUSTMENT,
                'reference_id' => $adjustment->id,
                'description' => $type === ManualAdjustmentType::ADD
                    ? "Admin cộng tiền thủ công: {$reason}"
                    : "Admin trừ tiền thủ công: {$reason}",
                'status' => WalletTransactionStatus::COMPLETED,
            ]);

            return $adjustment;
        });
    }
}
