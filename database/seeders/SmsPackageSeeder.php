<?php

namespace Database\Seeders;

use App\Models\SmsPackage;
use Illuminate\Database\Seeder;

class SmsPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'پکیج کوچک',
                'slug' => 'small',
                'description' => 'مناسب برای استفاده شخصی و کسب‌وکارهای کوچک',
                'sms_count' => 100,
                'validity_days' => 30,
                'price' => 50000,
                'discount_price' => 40000,
                'discount_percentage' => 20,
                'has_discount' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'پکیج متوسط',
                'slug' => 'medium',
                'description' => 'مناسب برای کسب‌وکارهای متوسط',
                'sms_count' => 500,
                'validity_days' => 60,
                'price' => 200000,
                'discount_price' => 150000,
                'discount_percentage' => 25,
                'has_discount' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'پکیج بزرگ',
                'slug' => 'large',
                'description' => 'مناسب برای کسب‌وکارهای بزرگ',
                'sms_count' => 1000,
                'validity_days' => 90,
                'price' => 350000,
                'discount_price' => 280000,
                'discount_percentage' => 20,
                'has_discount' => true,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'پکیج نامحدود',
                'slug' => 'unlimited',
                'description' => 'برای کسب‌وکارهای حرفه‌ای',
                'sms_count' => 5000,
                'validity_days' => 180,
                'price' => 1500000,
                'discount_price' => 1200000,
                'discount_percentage' => 20,
                'has_discount' => true,
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($packages as $package) {
            SmsPackage::create($package);
        }
    }
}

