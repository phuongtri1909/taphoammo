<?php

namespace App\Models;

use App\Enums\ManualAdjustmentType;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ManualWalletAdjustment extends Model
{
    use HasFactory, HasSlug, SoftDeletes;

    protected $fillable = [
        'user_id',
        'slug',
        'adjustment_type',
        'amount',
        'reason',
        'admin_note',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'adjustment_type' => ManualAdjustmentType::class,
        'processed_at' => 'datetime',
    ];

    protected string $slugPrefix = 'mwa';
    protected int $slugMaxLength = 15;
    protected int $randomStringLength = 12;
    protected bool $regenerateSlugOnUpdate = false;

    protected function getSlugSourceValue(): string
    {
        return '';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function walletTransaction()
    {
        return $this->hasOne(WalletTransaction::class, 'reference_id')
            ->where('reference_type', \App\Enums\WalletTransactionReferenceType::MANUAL_ADJUSTMENT->value);
    }
}
