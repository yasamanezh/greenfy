<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Enums\ModuleCategory;
use App\Enums\PlanDuration;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            [
                'name' => 'صفحه پرداخت',
                'slug' => 'payment-page',
                'description' => 'ماژول ساخت صفحه پرداخت',
                'version' => '1.0.0',
                'author' => 'LaraSmart',
                'category' => ModuleCategory::PAYMENT,
                'icon' => 'credit-card',
                'is_active' => true,
                'is_premium' => true,
                'price' => 500000,
                'discount_price' => 350000,
                'discount_percentage' => 30,
                'has_discount' => true,
                'duration' => PlanDuration::ANNUAL,
                'validity_days' => 365,
                'config' => ['enabled' => true],
                'sort_order' => 1,
            ],
            [
                'name' => 'پنل مدیریت',
                'slug' => 'admin-panel',
                'description' => 'ماژول پنل مدیریت پیشرفته',
                'version' => '1.0.0',
                'author' => 'LaraSmart',
                'category' => ModuleCategory::OTHER,
                'icon' => 'settings',
                'is_active' => true,
                'is_premium' => true,
                'price' => 1000000,
                'discount_price' => 750000,
                'discount_percentage' => 25,
                'has_discount' => true,
                'duration' => PlanDuration::ANNUAL,
                'validity_days' => 365,
                'config' => ['enabled' => true],
                'sort_order' => 2,
            ],
            [
                'name' => 'سیستم آنالیتیکس',
                'slug' => 'analytics',
                'description' => 'ماژول سیستم آنالیز و گزارش‌گیری',
                'version' => '1.0.0',
                'author' => 'LaraSmart',
                'category' => ModuleCategory::ANALYTICS,
                'icon' => 'chart-line',
                'is_active' => true,
                'is_premium' => true,
                'price' => 800000,
                'discount_price' => 600000,
                'discount_percentage' => 25,
                'has_discount' => true,
                'duration' => PlanDuration::ANNUAL,
                'validity_days' => 365,
                'config' => ['enabled' => true],
                'sort_order' => 3,
            ],
            [
                'name' => 'سیستم بازاریابی',
                'slug' => 'marketing',
                'description' => 'ماژول بازاریابی و تبلیغات',
                'version' => '1.0.0',
                'author' => 'LaraSmart',
                'category' => ModuleCategory::MARKETING,
                'icon' => 'bullhorn',
                'is_active' => true,
                'is_premium' => true,
                'price' => 600000,
                'discount_price' => 450000,
                'discount_percentage' => 25,
                'has_discount' => true,
                'duration' => PlanDuration::ANNUAL,
                'validity_days' => 365,
                'config' => ['enabled' => true],
                'sort_order' => 4,
            ],
            [
                'name' => 'سیستم امنیتی',
                'slug' => 'security',
                'description' => 'ماژول امنیت و محافظت',
                'version' => '1.0.0',
                'author' => 'LaraSmart',
                'category' => ModuleCategory::SECURITY,
                'icon' => 'shield',
                'is_active' => true,
                'is_premium' => true,
                'price' => 700000,
                'discount_price' => 525000,
                'discount_percentage' => 25,
                'has_discount' => true,
                'duration' => PlanDuration::ANNUAL,
                'validity_days' => 365,
                'config' => ['enabled' => true],
                'sort_order' => 5,
            ],
        ];

        foreach ($modules as $module) {
            Module::create($module);
        }
    }
}

