<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'phone',
        'subject',
        'message',
        'ip_address',
        'user_agent',
        'user_id',
        'read_at',
        'responded_at',
        'admin_response',
        'responded_by',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function isResponded(): bool
    {
        return $this->responded_at !== null;
    }

    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    public function markAsResponded(int $adminId, string $response): void
    {
        $this->update([
            'responded_at' => now(),
            'admin_response' => $response,
            'responded_by' => $adminId,
        ]);
    }
}
