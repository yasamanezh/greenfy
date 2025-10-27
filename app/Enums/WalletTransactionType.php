<?php

namespace App\Enums;

enum WalletTransactionType: int
{
    case DEPOSIT = 1;
    case WITHDRAWAL = 2;
    case PURCHASE = 3;
    case REFUND = 4;
    case COMMISSION = 5;

    public function isDeposit(): bool
    {
        return $this === self::DEPOSIT;
    }

    public function isWithdrawal(): bool
    {
        return $this === self::WITHDRAWAL;
    }

    public function isPurchase(): bool
    {
        return $this === self::PURCHASE;
    }

    public function isRefund(): bool
    {
        return $this === self::REFUND;
    }

    public function isCommission(): bool
    {
        return $this === self::COMMISSION;
    }

    public function getLabelText(): string
    {
        return match ($this) {
            self::DEPOSIT => 'واریز',
            self::WITHDRAWAL => 'برداشت',
            self::PURCHASE => 'خرید',
            self::REFUND => 'عودت',
            self::COMMISSION => 'کمیسیون'
        };
    }

    public function getLabelColor(): string
    {
        return match ($this) {
            self::DEPOSIT => 'success',
            self::WITHDRAWAL => 'danger',
            self::PURCHASE => 'info',
            self::REFUND => 'warning',
            self::COMMISSION => 'primary'
        };
    }

    public function getFrontLabelColor(): string
    {
        return match ($this) {
            self::DEPOSIT => 'bg-success',
            self::WITHDRAWAL => 'bg-danger',
            self::PURCHASE => 'bg-info',
            self::REFUND => 'bg-warning',
            self::COMMISSION => 'bg-primary'
        };
    }
}