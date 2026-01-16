<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefundItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'refund_id',
        'product_value_id',
        'amount',
    ];

    protected $casts = [
        'refund_id' => 'integer',
        'product_value_id' => 'integer',
        'amount' => 'decimal:2',
    ];

    public function refund(): BelongsTo
    {
        return $this->belongsTo(Refund::class);
    }

    public function productValue(): BelongsTo
    {
        return $this->belongsTo(ProductValue::class);
    }
}