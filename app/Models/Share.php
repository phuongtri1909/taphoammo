<?php

namespace App\Models;

use App\Enums\ShareStatus;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Share extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'image',
        'share_category_id',
        'user_id',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'views',
    ];

    protected $casts = [
        'status' => ShareStatus::class,
        'approved_at' => 'datetime',
        'views' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = static::generateUniqueSlug($model->title);
            }
        });

        static::deleting(function ($model) {
            if ($model->image && Storage::disk('public')->exists($model->image)) {
                Storage::disk('public')->delete($model->image);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ShareCategory::class, 'share_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', ShareStatus::APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', ShareStatus::PENDING);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', ShareStatus::DRAFT);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopePublished($query)
    {
        return $query->where('status', ShareStatus::APPROVED)
            ->orderByDesc('approved_at');
    }

    public function isApproved(): bool
    {
        return $this->status === ShareStatus::APPROVED;
    }

    public function isPending(): bool
    {
        return $this->status === ShareStatus::PENDING;
    }

    public function isDraft(): bool
    {
        return $this->status === ShareStatus::DRAFT;
    }

    public function isHidden(): bool
    {
        return $this->status === ShareStatus::HIDDEN;
    }

    public function isRejected(): bool
    {
        return $this->status === ShareStatus::REJECTED;
    }

    public function canEdit(): bool
    {
        return in_array($this->status, [ShareStatus::DRAFT, ShareStatus::REJECTED, ShareStatus::HIDDEN]);
    }

    public function canSubmit(): bool
    {
        return in_array($this->status, [ShareStatus::DRAFT, ShareStatus::REJECTED]);
    }

    public function canHide(): bool
    {
        return $this->status === ShareStatus::APPROVED;
    }

    public function canUnhide(): bool
    {
        return $this->status === ShareStatus::HIDDEN;
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return Storage::url($this->image);
        }
        return null;
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function getExcerptOrContent(int $length = 150): string
    {
        if ($this->excerpt) {
            return $this->excerpt;
        }
        
        $plainContent = strip_tags($this->content);
        return mb_strlen($plainContent) > $length 
            ? mb_substr($plainContent, 0, $length) . '...' 
            : $plainContent;
    }
}
