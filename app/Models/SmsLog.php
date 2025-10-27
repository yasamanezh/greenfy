<?php

namespace App\Models;

use App\Enums\SmsStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SmsLog extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_website_id',
        'sms_provider_id',
        'to',
        'message',
        'message_id',
        'status',
        'provider_response',
        'cost',
        'sms_count',
    ];

    protected $casts = [
        'cost' => 'decimal:4',
        'provider_response' => 'array',
        'status' => SmsStatus::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['to', 'status', 'cost', 'sms_count'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "پیامک به {$this->to} {$eventName} شد")
            ->useLogName('sms_logs');
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

    public function smsProvider(): BelongsTo
    {
        return $this->belongsTo(SmsProvider::class);
    }

    public function markAsDelivered(): void
    {
        $this->update(['status' => SmsStatus::DELIVERED]);
        
        activity()
            ->performedOn($this)
            ->log('پیامک تحویل داده شد');
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => SmsStatus::FAILED]);
        
        activity()
            ->performedOn($this)
            ->log('پیامک ناموفق بود');
    }

    // Scope برای دسترسی کاربر
    public function scopeVisibleTo($query, User $user)
    {
        return $query->whereHas('userWebsite', function($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }

    // Scopes
    public function scopeDelivered($query)
    {
        return $query->where('status', SmsStatus::DELIVERED->value);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', SmsStatus::FAILED->value);
    }
}