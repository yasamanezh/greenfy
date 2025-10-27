<?php

namespace App\Models;

use App\Enums\ModuleCategory;
use App\Enums\PlanDuration;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Module extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'version',
        'author',
        'category',
        'icon',
        'is_active',
        'is_premium',
        'price',
        'discount_price',
        'discount_percentage',
        'has_discount',
        'duration',
        'validity_days',
        'config',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_premium' => 'boolean',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'has_discount' => 'boolean',
        'config' => 'array',
        'category' => ModuleCategory::class,
        'duration' => PlanDuration::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'slug', 'price', 'is_active', 'is_premium'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "ماژول {$this->name} {$eventName} شد")
            ->useLogName('modules');
    }

    // Attribute Accessors
    public function getCategoryLabelAttribute(): string
    {
        return $this->category->getLabelText();
    }

    public function getCategoryColorAttribute(): string
    {
        return $this->category->getLabelColor();
    }

    public function getCategoryFrontColorAttribute(): string
    {
        return $this->category->getFrontLabelColor();
    }

    public function getDurationLabelAttribute(): string
    {
        return $this->duration->getLabelText();
    }

    public function getDurationDaysAttribute(): int
    {
        return $this->duration->getDays();
    }

    public function websiteModules(): HasMany
    {
        return $this->hasMany(WebsiteModule::class);
    }

    public function dependencies(): HasMany
    {
        return $this->hasMany(ModuleDependency::class, 'module_id');
    }

    public function requiredBy(): HasMany
    {
        return $this->hasMany(ModuleDependency::class, 'required_module_id');
    }

    public function getFinalPriceAttribute()
    {
        return $this->has_discount ? $this->discount_price : $this->price;
    }

    // Scope برای دسترسی کاربر (ماژول‌های فعال)
    public function scopeVisibleTo($query, User $user)
    {
        return $query->where('is_active', true);
    }

    // Scopes
    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    public function scopeByCategory($query, ModuleCategory $category)
    {
        return $query->where('category', $category->value);
    }
}