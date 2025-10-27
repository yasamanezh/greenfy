<?php

namespace App\Enums;

enum WebsiteStatus: int
{
    case ACTIVE = 1;
    case SUSPENDED = 2;
    case INACTIVE = 3;
    case BUILDING = 4;

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isSuspended(): bool
    {
        return $this === self::SUSPENDED;
    }

    public function isInactive(): bool
    {
        return $this === self::INACTIVE;
    }

    public function isBuilding(): bool
    {
        return $this === self::BUILDING;
    }

    public function getLabelText(): string
    {
        return match ($this) {
            self::ACTIVE => 'فعال',
            self::SUSPENDED => 'معلق',
            self::INACTIVE => 'غیرفعال',
            self::BUILDING => 'در حال ساخت'
        };
    }

    public function getLabelColor(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::SUSPENDED => 'warning',
            self::INACTIVE => 'danger',
            self::BUILDING => 'info'
        };
    }

    public function getFrontLabelColor(): string
    {
        return match ($this) {
            self::ACTIVE => 'bg-success',
            self::SUSPENDED => 'bg-warning',
            self::INACTIVE => 'bg-danger',
            self::BUILDING => 'bg-info'
        };
    }
}