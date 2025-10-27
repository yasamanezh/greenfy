<?php

namespace App\Models;

use App\Enums\WalletTransactionType;
use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class WalletTransaction extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'wallet_id',
        'user_website_id',
        'type',
        'amount',
        'balance_after',
        'description',
        'reference_type',
        'reference_id',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'type' => WalletTransactionType::class,
        'status' => TransactionStatus::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['type', 'amount', 'balance_after', 'status'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "تراکنش کیف پول {$this->id} {$eventName} شد")
            ->useLogName('wallet_transactions');
    }

    // Attribute Accessors
    public function getTypeLabelAttribute(): string
    {
        return $this->type->getLabelText();
    }

    public function getTypeColorAttribute(): string
    {
        return $this->type->getLabelColor();
    }

    public function getTypeFrontColorAttribute(): string
    {
        return $this->type->getFrontLabelColor();
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->getLabelText();
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->getLabelColor();
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function userWebsite(): BelongsTo
    {
        return $this->belongsTo(UserWebsite::class);
    }

    // Scope برای دسترسی کاربر
    public function scopeVisibleTo($query, User $user)
    {
        return $query->whereHas('wallet', function($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }

    // Scopes
    public function scopeDeposits($query)
    {
        return $query->where('type', WalletTransactionType::DEPOSIT->value);
    }

    public function scopePurchases($query)
    {
        return $query->where('type', WalletTransactionType::PURCHASE->value);
    }
}