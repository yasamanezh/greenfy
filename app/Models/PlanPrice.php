<?php

namespace App\Models;

use App\Enums\PlanDuration;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'duration',
        'price',
        'original_price',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'is_active' => 'boolean',
        'duration' => PlanDuration::class,
    ];

    // Attribute Accessors
    public function getDurationLabelAttribute(): string
    {
        return $this->duration->getLabelText();
    }

    public function getDurationDaysAttribute(): int
    {
        return $this->duration->getDays();
    }

    public function getDurationColorAttribute(): string
    {
        return $this->duration->getLabelColor();
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function getFinalPriceAttribute()
    {
        return $this->original_price ?: $this->price;
    }

    public function hasDiscount(): bool
    {
        return !is_null($this->original_price) && $this->original_price > $this->price;
    }

    public function getDiscountPercentageAttribute()
    {
        if (!$this->hasDiscount()) {
            return 0;
        }

        return round((($this->original_price - $this->price) / $this->original_price) * 100, 2);
    }
}