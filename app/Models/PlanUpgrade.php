<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PlanUpgrade extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'user_website_id',
        'from_website_subscription_id',
        'to_plan_id',
        'to_plan_price_id',
        'upgrade_price',
        'paid_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'upgrade_price' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'status' => TransactionStatus::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['upgrade_price', 'paid_amount', 'status'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "ارتقای پلن {$eventName} شد")
            ->useLogName('plan_upgrades');
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

    public function userWebsite(): BelongsTo
    {
        return $this->belongsTo(UserWebsite::class);
    }

    public function fromSubscription(): BelongsTo
    {
        return $this->belongsTo(WebsiteSubscription::class, 'from_website_subscription_id');
    }

    public function toPlan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'to_plan_id');
    }

    public function toPlanPrice(): BelongsTo
    {
        return $this->belongsTo(PlanPrice::class, 'to_plan_price_id');
    }

    // Scope برای دسترسی کاربر
    public function scopeVisibleTo($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    // Helper Methods
    public function markAsCompleted(): void
    {
        $this->update(['status' => TransactionStatus::COMPLETED]);
        
        activity()
            ->performedOn($this)
            ->log('ارتقای پلن با موفقیت انجام شد');
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => TransactionStatus::FAILED]);
        
        activity()
            ->performedOn($this)
            ->log('ارتقای پلن ناموفق بود');
    }
}