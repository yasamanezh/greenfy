<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SmsPackage extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'sms_count',
        'validity_days',
        'price',
        'discount_price',
        'discount_percentage',
        'has_discount',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'has_discount' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'price', 'sms_count', 'is_active', 'has_discount'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "پکیج پیامک {$this->name} {$eventName} شد")
            ->useLogName('sms_packages');
    }

    // Attribute Accessors
    public function getFinalPriceAttribute(): float
    {
        return $this->has_discount ? $this->discount_price : $this->price;
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price) . ' تومان';
    }

    public function getFormattedDiscountPriceAttribute(): string
    {
        return $this->has_discount ? number_format($this->discount_price) . ' تومان' : '-';
    }

    public function getFormattedFinalPriceAttribute(): string
    {
        return number_format($this->final_price) . ' تومان';
    }

    public function getPricePerSmsAttribute(): float
    {
        return $this->sms_count > 0 ? $this->final_price / $this->sms_count : 0;
    }

    public function getFormattedPricePerSmsAttribute(): string
    {
        return number_format($this->price_per_sms) . ' تومان';
    }

    public function getSavingsAmountAttribute(): float
    {
        return $this->has_discount ? $this->price - $this->discount_price : 0;
    }

    public function getFormattedSavingsAttribute(): string
    {
        return $this->has_discount ? number_format($this->savings_amount) . ' تومان' : '-';
    }

    public function getPopularityBadgeAttribute(): string
    {
        if ($this->sms_count >= 1000) return 'پرفروش';
        if ($this->sms_count >= 500) return 'محبوب';
        if ($this->has_discount) return 'تخفیف ویژه';
        return 'جدید';
    }

    public function getPopularityColorAttribute(): string
    {
        if ($this->sms_count >= 1000) return 'danger';
        if ($this->sms_count >= 500) return 'warning';
        if ($this->has_discount) return 'success';
        return 'info';
    }

    // Relationships
    public function smsTransactions(): HasMany
    {
        return $this->hasMany(WebsiteSmsTransaction::class);
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

    public function scopeWithDiscount($query)
    {
        return $query->where('has_discount', true);
    }

    public function scopeBySmsCount($query, $minCount = null, $maxCount = null)
    {
        if ($minCount) {
            $query->where('sms_count', '>=', $minCount);
        }
        if ($maxCount) {
            $query->where('sms_count', '<=', $maxCount);
        }
        return $query;
    }

    public function scopeCheapest($query)
    {
        return $query->orderByRaw('CASE WHEN has_discount = 1 THEN discount_price ELSE price END');
    }

    // Business Logic Methods
    public function calculatePriceForQuantity($quantity): float
    {
        $pricePerSms = $this->price_per_sms;
        return $pricePerSms * $quantity;
    }

    public function isBetterDealThan(SmsPackage $otherPackage): bool
    {
        return $this->price_per_sms < $otherPackage->price_per_sms;
    }

    public function getRecommendedFor(): string
    {
        if ($this->sms_count <= 100) return 'استفاده شخصی';
        if ($this->sms_count <= 500) return 'کسب‌وکارهای کوچک';
        if ($this->sms_count <= 2000) return 'کسب‌وکارهای متوسط';
        return 'کسب‌وکارهای بزرگ';
    }

    public function getPurchaseCount(): int
    {
        return $this->smsTransactions()
            ->where('status', TransactionStatus::COMPLETED)
            ->count();
    }

    // Static Methods
    public static function getBestDeal()
    {
        return static::active()
            ->get()
            ->sortBy('price_per_sms')
            ->first();
    }

    public static function getPopularPackages($limit = 3)
    {
        return static::active()
            ->withCount(['smsTransactions as purchases_count' => function($query) {
                $query->where('status', TransactionStatus::COMPLETED);
            }])
            ->orderBy('purchases_count', 'desc')
            ->limit($limit)
            ->get();
    }
}