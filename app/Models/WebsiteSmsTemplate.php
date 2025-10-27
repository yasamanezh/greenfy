<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class WebsiteSmsTemplate extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_website_id',
        'sms_template_id',
        'custom_content',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['custom_content', 'is_active'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "قالب پیامک سایت {$this->userWebsite->name} {$eventName} شد")
            ->useLogName('website_sms_templates');
    }

    public function userWebsite(): BelongsTo
    {
        return $this->belongsTo(UserWebsite::class);
    }

    public function smsTemplate(): BelongsTo
    {
        return $this->belongsTo(SmsTemplate::class);
    }

    // Scope برای دسترسی کاربر
    public function scopeVisibleTo($query, User $user)
    {
        return $query->whereHas('userWebsite', function($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }

    // Helper Methods
    public function getContent(): string
    {
        return $this->custom_content ?: $this->smsTemplate->content;
    }

    public function compileContent(array $variables = []): string
    {
        $content = $this->getContent();
        
        foreach ($variables as $key => $value) {
            $content = str_replace("{{{$key}}}", $value, $content);
        }
        
        return $content;
    }
}