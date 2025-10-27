<?php

namespace App\Enums;

enum ModuleCategory: int
{
    case PAYMENT = 1;
    case MARKETING = 2;
    case ANALYTICS = 3;
    case SEO = 4;
    case SECURITY = 5;
    case DESIGN = 6;
    case INTEGRATION = 7;
    case OTHER = 8;

    public function isPayment(): bool
    {
        return $this === self::PAYMENT;
    }

    public function isMarketing(): bool
    {
        return $this === self::MARKETING;
    }

    public function isAnalytics(): bool
    {
        return $this === self::ANALYTICS;
    }

    public function isSeo(): bool
    {
        return $this === self::SEO;
    }

    public function isSecurity(): bool
    {
        return $this === self::SECURITY;
    }

    public function isDesign(): bool
    {
        return $this === self::DESIGN;
    }

    public function isIntegration(): bool
    {
        return $this === self::INTEGRATION;
    }

    public function isOther(): bool
    {
        return $this === self::OTHER;
    }

    public function getLabelText(): string
    {
        return match ($this) {
            self::PAYMENT => 'پرداخت',
            self::MARKETING => 'بازاریابی',
            self::ANALYTICS => 'آنالیتیکس',
            self::SEO => 'سئو',
            self::SECURITY => 'امنیت',
            self::DESIGN => 'طراحی',
            self::INTEGRATION => 'یکپارچه‌سازی',
            self::OTHER => 'سایر'
        };
    }

    public function getLabelColor(): string
    {
        return match ($this) {
            self::PAYMENT => 'success',
            self::MARKETING => 'info',
            self::ANALYTICS => 'primary',
            self::SEO => 'warning',
            self::SECURITY => 'danger',
            self::DESIGN => 'secondary',
            self::INTEGRATION => 'dark',
            self::OTHER => 'light'
        };
    }

    public function getFrontLabelColor(): string
    {
        return match ($this) {
            self::PAYMENT => 'bg-success',
            self::MARKETING => 'bg-info',
            self::ANALYTICS => 'bg-primary',
            self::SEO => 'bg-warning',
            self::SECURITY => 'bg-danger',
            self::DESIGN => 'bg-secondary',
            self::INTEGRATION => 'bg-dark',
            self::OTHER => 'bg-light'
        };
    }
}