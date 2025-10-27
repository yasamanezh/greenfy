<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Transaction extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'user_website_id',
        'transactionable_type',
        'transactionable_id',
        'payment_token',
        'amount',
        'gateway',
        'status',
        'gateway_response',
        'wallet_used',
        'wallet_amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'wallet_amount' => 'decimal:2',
        'wallet_used' => 'boolean',
        'gateway_response' => 'array',
        'status' => TransactionStatus::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['amount', 'gateway', 'status', 'wallet_used'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "تراکنش {$this->id} {$eventName} شد")
            ->useLogName('transactions');
    }

    // Attribute Accessors
    public function getStatusLabelAttribute(): string
    {
        return $this->status->getLabelText();
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->getLabelColor();
    }

    public function getStatusFrontColorAttribute(): string
    {
        return $this->status->getFrontLabelColor();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userWebsite(): BelongsTo
    {
        return $this->belongsTo(UserWebsite::class);
    }

    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function markAsCompleted(): void
    {
        $this->update(['status' => TransactionStatus::COMPLETED]);
        
        activity()
            ->performedOn($this)
            ->log('تراکنش با موفقیت تکمیل شد');
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => TransactionStatus::FAILED]);
        
        activity()
            ->performedOn($this)
            ->log('تراکنش ناموفق بود');
    }

    // Scope برای دسترسی کاربر
    public function scopeVisibleTo($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', TransactionStatus::COMPLETED->value);
    }

    public function scopePending($query)
    {
        return $query->where('status', TransactionStatus::PENDING->value);
    }
}