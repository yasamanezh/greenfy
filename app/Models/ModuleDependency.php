<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ModuleDependency extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'module_id',
        'required_module_id',
        'min_version',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['module_id', 'required_module_id', 'min_version'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "وابستگی ماژول {$eventName} شد")
            ->useLogName('module_dependencies');
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function requiredModule(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'required_module_id');
    }

    // Scope برای دسترسی کاربر
    public function scopeVisibleTo($query, User $user)
    {
        return $query->whereHas('module', function($q) use ($user) {
            $q->where('is_active', true);
        });
    }
}