<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class WebsiteSetting extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_website_id',
        'key',
        'value',
        'type',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['key', 'value'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "تنظیمات سایت {$this->userWebsite->name} {$eventName} شد")
            ->useLogName('website_settings');
    }

    public function userWebsite(): BelongsTo
    {
        return $this->belongsTo(UserWebsite::class);
    }

    // Scope برای دسترسی کاربر
    public function scopeVisibleTo($query, User $user)
    {
        return $query->whereHas('userWebsite', function($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }

    // Helper Methods
    public function getValueAttribute($value)
    {
        return match ($this->type) {
            'array' => json_decode($value, true),
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'float' => (float) $value,
            default => $value
        };
    }

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = match ($this->type) {
            'array' => json_encode($value),
            'boolean' => (int) $value,
            default => $value
        };
    }
}