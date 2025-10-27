<?php

namespace App\Models;

use App\Enums\WalletTransactionType;
use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Wallet extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'balance',
        'total_earned',
        'total_spent',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'total_earned' => 'decimal:2',
        'total_spent' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['balance', 'total_earned', 'total_spent'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "کیف پول کاربر {$this->user->name} {$eventName} شد")
            ->useLogName('wallets');
    }

    // Attribute Accessors
    public function getFormattedBalanceAttribute(): string
    {
        return number_format($this->balance) . ' تومان';
    }

    public function getFormattedTotalEarnedAttribute(): string
    {
        return number_format($this->total_earned) . ' تومان';
    }

    public function getFormattedTotalSpentAttribute(): string
    {
        return number_format($this->total_spent) . ' تومان';
    }

    public function getBalanceColorAttribute(): string
    {
        if ($this->balance > 100000) return 'success';
        if ($this->balance > 50000) return 'info';
        if ($this->balance > 10000) return 'warning';
        return 'danger';
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    // Scopes
    public function scopeVisibleTo($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeWithBalance($query, $minBalance = 0)
    {
        return $query->where('balance', '>=', $minBalance);
    }

    // Business Logic Methods
    public function canAfford($amount): bool
    {
        return $this->balance >= $amount;
    }

    public function deposit($amount, $description = null): bool
    {
        if ($amount <= 0) {
            return false;
        }

        $this->increment('balance', $amount);
        $this->increment('total_earned', $amount);

        // ثبت تراکنش
        WalletTransaction::create([
            'wallet_id' => $this->id,
            'type' => WalletTransactionType::DEPOSIT,
            'amount' => $amount,
            'balance_after' => $this->balance,
            'description' => $description ?? 'واریز به کیف پول',
            'status' => TransactionStatus::COMPLETED,
        ]);

        activity()
            ->performedOn($this)
            ->withProperties(['amount' => $amount])
            ->log('مبلغ ' . number_format($amount) . ' تومان به کیف پول واریز شد');

        return true;
    }

    public function withdraw($amount, $description = null): bool
    {
        if (!$this->canAfford($amount) || $amount <= 0) {
            return false;
        }

        $this->decrement('balance', $amount);
        $this->increment('total_spent', $amount);

        // ثبت تراکنش
        WalletTransaction::create([
            'wallet_id' => $this->id,
            'type' => WalletTransactionType::PURCHASE,
            'amount' => -$amount,
            'balance_after' => $this->balance,
            'description' => $description ?? 'خرید از کیف پول',
            'status' => TransactionStatus::COMPLETED,
        ]);

        activity()
            ->performedOn($this)
            ->withProperties(['amount' => $amount])
            ->log('مبلغ ' . number_format($amount) . ' تومان از کیف پول کسر شد');

        return true;
    }

    public function refund($amount, $description = null): bool
    {
        if ($amount <= 0) {
            return false;
        }

        $this->increment('balance', $amount);
        $this->decrement('total_spent', $amount);

        // ثبت تراکنش
        WalletTransaction::create([
            'wallet_id' => $this->id,
            'type' => WalletTransactionType::REFUND,
            'amount' => $amount,
            'balance_after' => $this->balance,
            'description' => $description ?? 'عودت مبلغ',
            'status' => TransactionStatus::COMPLETED,
        ]);

        activity()
            ->performedOn($this)
            ->withProperties(['amount' => $amount])
            ->log('مبلغ ' . number_format($amount) . ' تومان به کیف پول عودت داده شد');

        return true;
    }

    public function getTransactionHistory($limit = 10)
    {
        return $this->transactions()
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getMonthlyStatistics()
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        return [
            'deposits' => $this->transactions()
                ->where('type', WalletTransactionType::DEPOSIT)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('amount'),
            'withdrawals' => $this->transactions()
                ->where('type', WalletTransactionType::PURCHASE)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('amount'),
            'transactions_count' => $this->transactions()
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count(),
        ];
    }

    // Static Methods
    public static function getUserWallet(User $user): self
    {
        return static::firstOrCreate(
            ['user_id' => $user->id],
            [
                'balance' => 0,
                'total_earned' => 0,
                'total_spent' => 0,
            ]
        );
    }
}