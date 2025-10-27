<?php

namespace App\Enums;

enum SubscriptionStatus: int
{
    case ACTIVE = 1;
    case EXPIRED = 2;
    case CANCELLED = 3;
    case SUSPENDED = 4;

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isExpired(): bool
    {
        return $this === self::EXPIRED;
    }

    public function isCancelled(): bool
    {
        return $this === self::CANCELLED;
    }

    public function isSuspended(): bool
    {
        return $this === self::SUSPENDED;
    }

    public function getLabelText(): string
    {
        return match ($this) {
            self::ACTIVE => 'فعال',
            self::EXPIRED => 'منقضی شده',
            self::CANCELLED => 'لغو شده',
            self::SUSPENDED => 'معلق'
        };
    }

    public function getLabelColor(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::EXPIRED => 'secondary',
            self::CANCELLED => 'danger',
            self::SUSPENDED => 'warning'
        };
    }

    public function getFrontLabelColor(): string
    {
        return match ($this) {
            self::ACTIVE => 'bg-success',
            self::EXPIRED => 'bg-secondary',
            self::CANCELLED => 'bg-danger',
            self::SUSPENDED => 'bg-warning'
        };
    }
}