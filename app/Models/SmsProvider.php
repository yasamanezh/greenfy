<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SmsProvider extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'driver',
        'config',
        'is_default',
        'is_active',
        'priority',
        'cost_per_sms',
    ];

    protected $casts = [
        'config' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'cost_per_sms' => 'decimal:4',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'driver', 'is_default', 'is_active'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "ارائه‌دهنده پیامک {$this->name} {$eventName} شد")
            ->useLogName('sms_providers');
    }

    public function smsLogs(): HasMany
    {
        return $this->hasMany(SmsLog::class);
    }

    // Scope برای دسترسی کاربر (ارائه‌دهندگان فعال)
    public function scopeVisibleTo($query, User $user)
    {
        return $query->where('is_active', true);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Helper Methods
    public function getConfigValue($key, $default = null)
    {
        return data_get($this->config, $key, $default);
    }
}