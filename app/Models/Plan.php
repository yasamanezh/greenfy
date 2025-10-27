<?php

namespace App\Models;

use App\Enums\PlanDuration;
use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Plan extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'is_popular',
        'is_recommended',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_popular' => 'boolean',
        'is_recommended' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'slug', 'is_popular', 'is_recommended', 'is_active'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "پلن {$this->name} {$eventName} شد")
            ->useLogName('plans');
    }

    // Attribute Accessors
    public function getDisplayNameAttribute(): string
    {
        $badges = [];
        if ($this->is_popular) $badges[] = '💎';
        if ($this->is_recommended) $badges[] = '⭐';
        
        return trim($this->name . ' ' . implode(' ', $badges));
    }

    public function getPopularityLevelAttribute(): string
    {
        if ($this->is_popular) return 'popular';
        if ($this->is_recommended) return 'recommended';
        return 'normal';
    }

    public function getColorClassAttribute(): string
    {
        return match($this->color) {
            'red' => 'bg-danger',
            'blue' => 'bg-primary',
            'green' => 'bg-success',
            'yellow' => 'bg-warning',
            'purple' => 'bg-purple',
            default => 'bg-secondary'
        };
    }

    public function getMonthlyPriceAttribute(): ?float
    {
        $monthlyPrice = $this->prices()
            ->where('duration', PlanDuration::MONTHLY)
            ->where('is_active', true)
            ->first();

        return $monthlyPrice?->price;
    }

    public function getFormattedMonthlyPriceAttribute(): string
    {
        return $this->monthly_price ? number_format($this->monthly_price) . ' تومان' : 'نامشخص';
    }

    public function getYearlyPriceAttribute(): ?float
    {
        $yearlyPrice = $this->prices()
            ->where('duration', PlanDuration::ANNUAL)
            ->where('is_active', true)
            ->first();

        return $yearlyPrice?->price;
    }

    public function getFormattedYearlyPriceAttribute(): string
    {
        return $this->yearly_price ? number_format($this->yearly_price) . ' تومان' : 'نامشخص';
    }

    // Relationships
    public function prices(): HasMany
    {
        return $this->hasMany(PlanPrice::class);
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'plan_feature')
                    ->withPivot('value', 'is_available', 'sort_order')
                    ->withTimestamps();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(WebsiteSubscription::class);
    }

    // Scopes
    public function scopeVisibleTo($query, User $user)
    {
        return $query->where('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeRecommended($query)
    {
        return $query->where('is_recommended', true);
    }

    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    // Business Logic Methods
    public function getActiveFeatures()
    {
        return $this->features()
            ->wherePivot('is_available', true)
            ->orderBy('plan_feature.sort_order')
            ->get();
    }

    public function getFeatureValue($featureName)
    {
        $feature = $this->features()
            ->where('name', $featureName)
            ->wherePivot('is_available', true)
            ->first();

        return $feature?->pivot?->value;
    }

    public function hasFeature($featureName): bool
    {
        return $this->features()
            ->where('name', $featureName)
            ->wherePivot('is_available', true)
            ->exists();
    }

    public function getPriceForDuration(PlanDuration $duration): ?PlanPrice
    {
        return $this->prices()
            ->where('duration', $duration)
            ->where('is_active', true)
            ->first();
    }

    public function getBestPrice(): ?PlanPrice
    {
        // بهترین قیمت (سالانه معمولاً ارزان‌تر است)
        return $this->prices()
            ->where('is_active', true)
            ->orderBy('price')
            ->first();
    }

    public function getSubscriptionCount(): int
    {
        return $this->subscriptions()
            ->where('status', SubscriptionStatus::ACTIVE)
            ->count();
    }

    public function isUpgradeFrom(Plan $otherPlan): bool
    {
        return $this->sort_order > $otherPlan->sort_order;
    }

    public function getUpgradePriceFrom(Plan $otherPlan, PlanDuration $duration): ?float
    {
        $currentPrice = $otherPlan->getPriceForDuration($duration)?->price;
        $newPrice = $this->getPriceForDuration($duration)?->price;

        if (!$currentPrice || !$newPrice) {
            return null;
        }

        return max(0, $newPrice - $currentPrice);
    }

    // Static Methods
    public static function getDefaultPlan()
    {
        return static::active()
            ->orderBy('sort_order')
            ->first();
    }

    public static function getPopularPlans($limit = 3)
    {
        return static::active()
            ->popular()
            ->with('prices')
            ->limit($limit)
            ->get();
    }

    public static function getRecommendedPlan()
    {
        return static::active()
            ->recommended()
            ->with('prices')
            ->first();
    }
}