<?php

namespace App\Models;

use App\Enums\SmsStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class WebsiteSmsCredit extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_website_id',
        'remaining_sms',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['remaining_sms', 'expires_at'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "اعتبار پیامک سایت {$this->userWebsite->name} {$eventName} شد")
            ->useLogName('sms_credits');
    }

    // Attribute Accessors
    public function getFormattedRemainingSmsAttribute(): string
    {
        return number_format($this->remaining_sms) . ' پیامک';
    }

    public function getExpiresAtFormattedAttribute(): string
    {
        return $this->expires_at ? $this->expires_at->format('Y/m/d H:i') : 'نامحدود';
    }

    public function getExpiresAtJalaliAttribute(): string
    {
        if (!$this->expires_at) {
            return 'نامحدود';
        }

        // اگر از پکیج ژالری استفاده می‌کنید
        // return jdate($this->expires_at)->format('Y/m/d H:i');
        
        return $this->expires_at->format('Y/m/d H:i');
    }

    public function getRemainingDaysAttribute(): int
    {
        if (!$this->expires_at) {
            return 9999; // عدد بزرگ برای نامحدود
        }

        return max(0, now()->diffInDays($this->expires_at, false));
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getIsUnlimitedAttribute(): bool
    {
        return is_null($this->expires_at);
    }

    public function getUsagePercentageAttribute(): float
    {
        // اگر تاریخ انقضا نداریم، فقط بر اساس تعداد پیامک محاسبه کنیم
        if ($this->is_unlimited) {
            return $this->remaining_sms > 1000 ? 100 : ($this->remaining_sms / 1000) * 100;
        }

        $totalDays = $this->created_at->diffInDays($this->expires_at);
        $passedDays = $this->created_at->diffInDays(now());

        return min(100, max(0, ($passedDays / $totalDays) * 100));
    }

    public function getStatusAttribute(): string
    {
        if ($this->is_expired) {
            return 'expired';
        }

        if ($this->remaining_sms <= 0) {
            return 'empty';
        }

        if ($this->remaining_days <= 3) {
            return 'warning';
        }

        return 'active';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'expired' => 'منقضی شده',
            'empty' => 'اتمام اعتبار',
            'warning' => 'در حال اتمام',
            'active' => 'فعال',
            default => 'نامشخص'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'expired' => 'danger',
            'empty' => 'secondary',
            'warning' => 'warning',
            'active' => 'success',
            default => 'info'
        };
    }

    public function getStatusFrontColorAttribute(): string
    {
        return match($this->status) {
            'expired' => 'bg-danger',
            'empty' => 'bg-secondary',
            'warning' => 'bg-warning',
            'active' => 'bg-success',
            default => 'bg-info'
        };
    }

    // Relationships
    public function userWebsite(): BelongsTo
    {
        return $this->belongsTo(UserWebsite::class);
    }

    // Scopes
    public function scopeVisibleTo($query, User $user)
    {
        return $query->whereHas('userWebsite', function($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }

    public function scopeActive($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        })->where('remaining_sms', '>', 0);
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    public function scopeLowCredit($query, $threshold = 10)
    {
        return $query->where('remaining_sms', '<=', $threshold);
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->whereBetween('expires_at', [now(), now()->addDays($days)]);
    }

    public function scopeByWebsite($query, $websiteId)
    {
        return $query->where('user_website_id', $websiteId);
    }

    // Business Logic Methods
    public function hasCredit($count = 1): bool
    {
        return $this->remaining_sms >= $count && !$this->is_expired;
    }

    public function decrementCredit($count = 1): bool
    {
        if (!$this->hasCredit($count)) {
            return false;
        }

        $this->decrement('remaining_sms', $count);

        activity()
            ->performedOn($this)
            ->withProperties(['count' => $count, 'remaining' => $this->remaining_sms])
            ->log("{$count} پیامک از اعتبار کسر شد");

        return true;
    }

    public function incrementCredit($count): bool
    {
        if ($count <= 0) {
            return false;
        }

        $this->increment('remaining_sms', $count);

        activity()
            ->performedOn($this)
            ->withProperties(['count' => $count, 'remaining' => $this->remaining_sms])
            ->log("{$count} پیامک به اعتبار اضافه شد");

        return true;
    }

    public function addCredit($count, $validityDays = null): bool
    {
        if ($count <= 0) {
            return false;
        }

        $this->increment('remaining_sms', $count);

        // اگر تاریخ انقضا مشخص شده، آن را بروزرسانی کن
        if ($validityDays) {
            $newExpiry = now()->addDays($validityDays);
            
            // اگر تاریخ انقضای جدید از تاریخ فعلی بیشتر است، آن را اعمال کن
            if (!$this->expires_at || $newExpiry->gt($this->expires_at)) {
                $this->update(['expires_at' => $newExpiry]);
            }
        }

        activity()
            ->performedOn($this)
            ->withProperties([
                'count' => $count,
                'validity_days' => $validityDays,
                'remaining' => $this->remaining_sms,
                'expires_at' => $this->expires_at
            ])
            ->log("{$count} پیامک با اعتبار {$validityDays} روز به اعتبار اضافه شد");

        return true;
    }

    public function resetCredit($count = 0, $expiresAt = null): void
    {
        $this->update([
            'remaining_sms' => $count,
            'expires_at' => $expiresAt,
        ]);

        activity()
            ->performedOn($this)
            ->withProperties([
                'count' => $count,
                'expires_at' => $expiresAt
            ])
            ->log('اعتبار پیامک ریست شد');
    }

    public function extendValidity($days): bool
    {
        if ($days <= 0) {
            return false;
        }

        $newExpiry = $this->expires_at 
            ? $this->expires_at->addDays($days)
            : now()->addDays($days);

        $this->update(['expires_at' => $newExpiry]);

        activity()
            ->performedOn($this)
            ->withProperties(['days' => $days, 'new_expiry' => $newExpiry])
            ->log("اعتبار پیامک به مدت {$days} روز تمدید شد");

        return true;
    }

    public function getUsageStatistics(): array
    {
        $totalUsed = $this->smsLogs()->count();
        $successful = $this->smsLogs()->where('status', SmsStatus::DELIVERED->value)->count();
        $failed = $this->smsLogs()->where('status', SmsStatus::FAILED->value)->count();

        return [
            'total_used' => $totalUsed,
            'successful' => $successful,
            'failed' => $failed,
            'success_rate' => $totalUsed > 0 ? round(($successful / $totalUsed) * 100, 2) : 0,
            'remaining' => $this->remaining_sms,
            'total_available' => $totalUsed + $this->remaining_sms,
            'usage_percentage' => $totalUsed > 0 ? round(($totalUsed / ($totalUsed + $this->remaining_sms)) * 100, 2) : 0,
        ];
    }

    public function getDailyUsage($days = 30): array
    {
        return $this->smsLogs()
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();
    }

    public function sendLowCreditNotification($threshold = 10): void
    {
        if ($this->remaining_sms <= $threshold && $this->remaining_sms > 0) {
            // ارسال نوتیفیکیشن به کاربر
            // می‌توانید از سیستم نوتیفیکیشن خود استفاده کنید
            activity()
                ->performedOn($this)
                ->withProperties(['remaining' => $this->remaining_sms, 'threshold' => $threshold])
                ->log('هشدار اتمام اعتبار پیامک ارسال شد');
        }
    }

    public function sendExpiryNotification($daysBefore = 3): void
    {
        if ($this->expires_at && $this->expires_at->diffInDays(now()) <= $daysBefore) {
            // ارسال نوتیفیکیشن به کاربر
            activity()
                ->performedOn($this)
                ->withProperties([
                    'expires_at' => $this->expires_at,
                    'remaining_days' => $this->remaining_days
                ])
                ->log('هشدار انقضای اعتبار پیامک ارسال شد');
        }
    }

    // Static Methods
    public static function getOrCreateForWebsite($websiteId): self
    {
        return static::firstOrCreate(
            ['user_website_id' => $websiteId],
            [
                'remaining_sms' => 0,
                'expires_at' => null,
            ]
        );
    }

    public static function getTotalCredits(): int
    {
        return static::sum('remaining_sms');
    }

    public static function getActiveWebsitesCount(): int
    {
        return static::active()->count();
    }

    // Event Handlers
    protected static function booted()
    {
        static::updated(function ($smsCredit) {
            // ارسال نوتیفیکیشن در صورت کم شدن اعتبار
            $smsCredit->sendLowCreditNotification();
            $smsCredit->sendExpiryNotification();
        });

        static::created(function ($smsCredit) {
            activity()
                ->performedOn($smsCredit)
                ->log('اعتبار پیامک جدید ایجاد شد');
        });
    }
}