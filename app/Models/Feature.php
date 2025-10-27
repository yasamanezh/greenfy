<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Feature extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'category',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'category', 'is_active'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "ویژگی {$this->name} {$eventName} شد")
            ->useLogName('features');
    }

    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class, 'plan_feature')
                    ->withPivot('value', 'is_available', 'sort_order')
                    ->withTimestamps();
    }

    // Scope برای دسترسی کاربر (ویژگی‌های فعال)
    public function scopeVisibleTo($query, User $user)
    {
        return $query->where('is_active', true);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}