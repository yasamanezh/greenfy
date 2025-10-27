<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Setting extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['key', 'value', 'group'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "تنظیمات {$this->key} {$eventName} شد")
            ->useLogName('settings');
    }

    // Scope برای دسترسی کاربر (فقط ادمین)
    public function scopeVisibleTo($query, User $user)
    {
        // فقط کاربران ادمین می‌توانند تنظیمات را ببینند
        return $query->when(!$user->is_admin, function($q) {
            $q->whereRaw('1 = 0'); // هیچ رکوردی نشان نده
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

    public static function getValue(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function setValue(string $key, $value, string $type = 'string', string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group
            ]
        );
    }
}