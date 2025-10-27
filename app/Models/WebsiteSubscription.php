<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class WebsiteSubscription extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_website_id',
        'plan_id',
        'plan_price_id',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'status' => SubscriptionStatus::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'start_date', 'end_date'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "اشتراک پلن {$this->plan->name} برای سایت {$this->userWebsite->name} {$eventName} شد")
            ->useLogName('subscriptions');
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

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function planPrice(): BelongsTo
    {
        return $this->belongsTo(PlanPrice::class);
    }

    public function isActive(): bool
    {
        return $this->status->isActive() && $this->end_date->isFuture();
    }

    // Scope برای دسترسی کاربر
    public function scopeVisibleTo($query, User $user)
    {
        return $query->whereHas('userWebsite', function($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', SubscriptionStatus::ACTIVE->value);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', SubscriptionStatus::EXPIRED->value);
    }
}