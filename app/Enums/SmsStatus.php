<?php

namespace App\Enums;

enum SmsStatus: int
{
    case SENT = 1;
    case DELIVERED = 2;
    case FAILED = 3;
    case UNKNOWN = 4;

    public function isSent(): bool
    {
        return $this === self::SENT;
    }

    public function isDelivered(): bool
    {
        return $this === self::DELIVERED;
    }

    public function isFailed(): bool
    {
        return $this === self::FAILED;
    }

    public function isUnknown(): bool
    {
        return $this === self::UNKNOWN;
    }

    public function getLabelText(): string
    {
        return match ($this) {
            self::SENT => 'ارسال شده',
            self::DELIVERED => 'تحویل شده',
            self::FAILED => 'ناموفق',
            self::UNKNOWN => 'نامشخص'
        };
    }

    public function getLabelColor(): string
    {
        return match ($this) {
            self::SENT => 'info',
            self::DELIVERED => 'success',
            self::FAILED => 'danger',
            self::UNKNOWN => 'secondary'
        };
    }

    public function getFrontLabelColor(): string
    {
        return match ($this) {
            self::SENT => 'bg-info',
            self::DELIVERED => 'bg-success',
            self::FAILED => 'bg-danger',
            self::UNKNOWN => 'bg-secondary'
        };
    }
}