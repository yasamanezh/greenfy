<?php

namespace App\Enums;

enum UpgradeStatus: int
{
    case PENDING = 1;
    case COMPLETED = 2;
    case FAILED = 3;

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function isCompleted(): bool
    {
        return $this === self::COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this === self::FAILED;
    }

    public function getLabelText(): string
    {
        return match ($this) {
            self::PENDING => 'در انتظار',
            self::COMPLETED => 'تکمیل شده',
            self::FAILED => 'ناموفق'
        };
    }

    public function getLabelColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::COMPLETED => 'success',
            self::FAILED => 'danger'
        };
    }

    public function getFrontLabelColor(): string
    {
        return match ($this) {
            self::PENDING => 'bg-warning',
            self::COMPLETED => 'bg-success',
            self::FAILED => 'bg-danger'
        };
    }
}