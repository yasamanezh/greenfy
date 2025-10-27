<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            [
                'name' => 'تعداد پیامک',
                'description' => 'تعداد پیامک قابل ارسال',
                'icon' => 'message',
                'category' => 'sms',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'پهنای باند',
                'description' => 'مقدار پهنای باند ماهانه',
                'icon' => 'server',
                'category' => 'hosting',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'فضای ذخیره‌سازی',
                'description' => 'مقدار فضای دیسک',
                'icon' => 'database',
                'category' => 'hosting',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'پشتیبانی',
                'description' => 'نوع پشتیبانی',
                'icon' => 'support',
                'category' => 'support',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'SSL',
                'description' => 'گواهینامه SSL رایگان',
                'icon' => 'lock',
                'category' => 'security',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'امنیت',
                'description' => 'نحوه امنیت سایت',
                'icon' => 'shield',
                'category' => 'security',
                'sort_order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($features as $feature) {
            Feature::create($feature);
        }
    }
}

