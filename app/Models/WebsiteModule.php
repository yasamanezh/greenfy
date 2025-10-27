<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class WebsiteModule extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_website_id',
        'module_id',
        'start_date',
        'end_date',
        'status',
        'config',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'config' => 'array',
        'status' => SubscriptionStatus::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'start_date', 'end_date', 'config'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "ماژول {$this->module->name} برای سایت {$this->userWebsite->name} {$eventName} شد")
            ->useLogName('website_modules');
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

    public function getRemainingDaysAttribute(): int
    {
        if (!$this->end_date || $this->end_date->isPast()) {
            return 0;
        }

        return now()->diffInDays($this->end_date, false);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->end_date && $this->end_date->isPast();
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status->isActive() && !$this->is_expired;
    }

    public function getConfigValue(string $key, $default = null)
    {
        return data_get($this->config, $key, $default);
    }

    public function setConfigValue(string $key, $value): void
    {
        $config = $this->config ?? [];
        data_set($config, $key, $value);
        $this->config = $config;
    }

    // Relationships
    public function userWebsite(): BelongsTo
    {
        return $this->belongsTo(UserWebsite::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
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
        return $query->where('status', SubscriptionStatus::ACTIVE->value)
                    ->where('end_date', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where(function($q) {
            $q->where('status', SubscriptionStatus::EXPIRED->value)
              ->orWhere('end_date', '<=', now());
        });
    }

    public function scopeByModule($query, $moduleId)
    {
        return $query->where('module_id', $moduleId);
    }

    public function scopeByWebsite($query, $websiteId)
    {
        return $query->where('user_website_id', $websiteId);
    }

    // Business Logic Methods
    public function activate(): bool
    {
        if ($this->module->is_premium && !$this->userWebsite->activeSubscription) {
            return false;
        }

        $this->update([
            'status' => SubscriptionStatus::ACTIVE,
            'start_date' => now(),
            'end_date' => now()->addDays($this->module->validity_days),
        ]);

        activity()
            ->performedOn($this)
            ->withProperties(['module' => $this->module->name])
            ->log('ماژول فعال شد');

        return true;
    }

    public function deactivate(): void
    {
        $this->update([
            'status' => SubscriptionStatus::CANCELLED,
        ]);

        activity()
            ->performedOn($this)
            ->withProperties(['module' => $this->module->name])
            ->log('ماژول غیرفعال شد');
    }

    public function renew(): bool
    {
        if (!$this->module->is_premium) {
            return false;
        }

        $this->update([
            'end_date' => $this->end_date->addDays($this->module->validity_days),
            'status' => SubscriptionStatus::ACTIVE,
        ]);

        activity()
            ->performedOn($this)
            ->withProperties([
                'module' => $this->module->name,
                'new_end_date' => $this->end_date
            ])
            ->log('ماژول تمدید شد');

        return true;
    }

    public function canBeActivated(): bool
    {
        // بررسی وابستگی‌ها
        $dependencies = $this->module->dependencies;
        foreach ($dependencies as $dependency) {
            $hasDependency = WebsiteModule::where('user_website_id', $this->user_website_id)
                ->where('module_id', $dependency->required_module_id)
                ->active()
                ->exists();

            if (!$hasDependency) {
                return false;
            }
        }

        return true;
    }

    public function getMissingDependencies(): array
    {
        $missing = [];
        $dependencies = $this->module->dependencies;

        foreach ($dependencies as $dependency) {
            $hasDependency = WebsiteModule::where('user_website_id', $this->user_website_id)
                ->where('module_id', $dependency->required_module_id)
                ->active()
                ->exists();

            if (!$hasDependency) {
                $missing[] = $dependency->requiredModule->name;
            }
        }

        return $missing;
    }

    public function getUsageStatistics(): array
    {
        // آمار استفاده از ماژول (می‌تواند بر اساس نوع ماژول متفاوت باشد)
        return [
            'activated_at' => $this->start_date,
            'days_used' => $this->start_date ? now()->diffInDays($this->start_date) : 0,
            'days_remaining' => $this->remaining_days,
            'is_active' => $this->is_active,
        ];
    }

    // Event Handlers
    protected static function booted()
    {
        static::creating(function ($websiteModule) {
            // تنظیم تاریخ‌های پیش‌فرض
            if (!$websiteModule->start_date) {
                $websiteModule->start_date = now();
            }
            if (!$websiteModule->end_date) {
                $websiteModule->end_date = now()->addDays($websiteModule->module->validity_days);
            }
        });

        static::updated(function ($websiteModule) {
            // اگر ماژول منقضی شده، وضعیت را به expired تغییر بده
            if ($websiteModule->end_date && $websiteModule->end_date->isPast() && $websiteModule->status->isActive()) {
                $websiteModule->update(['status' => SubscriptionStatus::EXPIRED]);
            }
        });
    }
}