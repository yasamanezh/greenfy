<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use App\Enums\WebsiteStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class UserWebsite extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'name',
        'domain',
        'subdomain',
        'description',
        'theme',
        'status',
    ];

    protected $casts = [
        'status' => WebsiteStatus::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'domain', 'subdomain', 'status'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "وبسایت {$this->name} {$eventName} شد")
            ->useLogName('websites');
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

    public function settings(): HasMany
    {
        return $this->hasMany(WebsiteSetting::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(WebsiteSubscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(WebsiteSubscription::class)->where('status', SubscriptionStatus::ACTIVE->value);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(WebsiteModule::class);
    }

    public function smsCredits(): HasOne
    {
        return $this->hasOne(WebsiteSmsCredit::class);
    }

    public function smsTransactions(): HasMany
    {
        return $this->hasMany(WebsiteSmsTransaction::class);
    }

    public function smsLogs(): HasMany
    {
        return $this->hasMany(SmsLog::class);
    }

    // Scope برای دسترسی کاربر
    public function scopeVisibleTo($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', WebsiteStatus::ACTIVE->value);
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', WebsiteStatus::SUSPENDED->value);
    }
}