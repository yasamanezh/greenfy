<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SmsTemplate extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'subject',
        'content',
        'variables',
        'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'slug', 'is_active'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "قالب پیامک {$this->name} {$eventName} شد")
            ->useLogName('sms_templates');
    }

    public function websiteTemplates(): HasMany
    {
        return $this->hasMany(WebsiteSmsTemplate::class);
    }

    // Scope برای دسترسی کاربر (قالب‌های فعال)
    public function scopeVisibleTo($query, User $user)
    {
        return $query->where('is_active', true);
    }

    // Helper Methods
    public function compileContent(array $variables = []): string
    {
        $content = $this->content;
        
        foreach ($variables as $key => $value) {
            $content = str_replace("{{{$key}}}", $value, $content);
        }
        
        return $content;
    }

    public function validateVariables(array $variables): bool
    {
        $requiredVariables = $this->variables ?? [];
        $missing = array_diff($requiredVariables, array_keys($variables));
        
        return empty($missing);
    }

    public function getMissingVariables(array $variables): array
    {
        $requiredVariables = $this->variables ?? [];
        return array_diff($requiredVariables, array_keys($variables));
    }
}