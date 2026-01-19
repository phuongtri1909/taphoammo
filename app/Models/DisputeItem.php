<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisputeItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispute_id',
        'product_value_id',
    ];

    protected $casts = [
        'dispute_id' => 'integer',
        'product_value_id' => 'integer',
    ];

    public function dispute(): BelongsTo
    {
        return $this->belongsTo(Dispute::class);
    }

    public function productValue(): BelongsTo
    {
        return $this->belongsTo(ProductValue::class);
    }
}
