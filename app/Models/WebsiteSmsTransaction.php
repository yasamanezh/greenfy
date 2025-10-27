<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class WebsiteSmsTransaction extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_website_id',
        'sms_package_id',
        'sms_count',
        'amount',
        'payment_token',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'status' => TransactionStatus::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['sms_count', 'amount', 'status'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "تراکنش پیامک {$eventName} شد")
            ->useLogName('sms_transactions');
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

    public function userWebsite(): BelongsTo
    {
        return $this->belongsTo(UserWebsite::class);
    }

    public function smsPackage(): BelongsTo
    {
        return $this->belongsTo(SmsPackage::class);
    }

    // Scope برای دسترسی کاربر
    public function scopeVisibleTo($query, User $user)
    {
        return $query->whereHas('userWebsite', function($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }

    // Helper Methods
    public function markAsCompleted(): void
    {
        $this->update(['status' => TransactionStatus::COMPLETED]);
        
        activity()
            ->performedOn($this)
            ->log('تراکنش پیامک با موفقیت تکمیل شد');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}