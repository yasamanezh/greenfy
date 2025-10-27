<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SmsBlacklist extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'sms_blacklist';

    protected $fillable = [
        'phone_number',
        'reason',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['phone_number', 'reason'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "شماره {$this->phone_number} {$eventName} لیست سیاه شد")
            ->useLogName('sms_blacklist');
    }

    // Scope برای دسترسی کاربر (فقط ادمین)
    public function scopeVisibleTo($query, User $user)
    {
        // فقط کاربران ادمین می‌توانند لیست سیاه را ببینند
        return $query->when(!$user->is_admin, function($q) {
            $q->whereRaw('1 = 0'); // هیچ رکوردی نشان نده
        });
    }

    // Helper Methods
    public static function isBlacklisted(string $phoneNumber): bool
    {
        return static::where('phone_number', $phoneNumber)->exists();
    }

    public static function addToBlacklist(string $phoneNumber, string $reason = null): self
    {
        return static::create([
            'phone_number' => $phoneNumber,
            'reason' => $reason
        ]);
    }

    public static function removeFromBlacklist(string $phoneNumber): bool
    {
        return static::where('phone_number', $phoneNumber)->delete();
    }
}