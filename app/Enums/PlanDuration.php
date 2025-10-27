<?php

namespace App\Enums;

enum PlanDuration: int
{
    case MONTHLY = 1;
    case QUARTERLY = 2;
    case SEMI_ANNUAL = 3;
    case ANNUAL = 4;

    public function isMonthly(): bool
    {
        return $this === self::MONTHLY;
    }

    public function isQuarterly(): bool
    {
        return $this === self::QUARTERLY;
    }

    public function isSemiAnnual(): bool
    {
        return $this === self::SEMI_ANNUAL;
    }

    public function isAnnual(): bool
    {
        return $this === self::ANNUAL;
    }

    public function getLabelText(): string
    {
        return match ($this) {
            self::MONTHLY => 'ماهانه',
            self::QUARTERLY => 'سه ماهه',
            self::SEMI_ANNUAL => 'شش ماهه',
            self::ANNUAL => 'سالانه'
        };
    }

    public function getDays(): int
    {
        return match ($this) {
            self::MONTHLY => 30,
            self::QUARTERLY => 90,
            self::SEMI_ANNUAL => 180,
            self::ANNUAL => 365
        };
    }

    public function getLabelColor(): string
    {
        return match ($this) {
            self::MONTHLY => 'primary',
            self::QUARTERLY => 'info',
            self::SEMI_ANNUAL => 'success',
            self::ANNUAL => 'warning'
        };
    }

    public function getFrontLabelColor(): string
    {
        return match ($this) {
            self::MONTHLY => 'bg-primary',
            self::QUARTERLY => 'bg-info',
            self::SEMI_ANNUAL => 'bg-success',
            self::ANNUAL => 'bg-warning'
        };
    }
}