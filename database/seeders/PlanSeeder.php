<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PlanPrice;
use App\Models\Feature;
use App\Enums\PlanDuration;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        // ایجاد ویژگی‌ها در صورت عدم وجود
        $smsFeature = Feature::firstOrCreate(
            ['name' => 'تعداد پیامک'],
            [
                'description' => 'تعداد پیامک قابل ارسال',
                'icon' => 'message',
                'category' => 'sms',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $bandwidthFeature = Feature::firstOrCreate(
            ['name' => 'پهنای باند'],
            [
                'description' => 'مقدار پهنای باند ماهانه',
                'icon' => 'server',
                'category' => 'hosting',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        $storageFeature = Feature::firstOrCreate(
            ['name' => 'فضای ذخیره‌سازی'],
            [
                'description' => 'مقدار فضای دیسک',
                'icon' => 'database',
                'category' => 'hosting',
                'sort_order' => 3,
                'is_active' => true,
            ]
        );

        // پلن عادی - 3 ماهه
        $planBasic = Plan::create([
            'name' => 'عادی',
            'slug' => 'basic',
            'description' => 'پلن عادی با تمام امکانات پایه',
            'color' => 'blue',
            'is_popular' => false,
            'is_recommended' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // ایجاد قیمت‌های پلن عادی
        PlanPrice::create([
            'plan_id' => $planBasic->id,
            'duration' => PlanDuration::QUARTERLY,
            'price' => 500000,
            'is_active' => true,
        ]);

        // پلن نقره‌ای - 6 ماهه
        $planSilver = Plan::create([
            'name' => 'نقره‌ای',
            'slug' => 'silver',
            'description' => 'پلن نقره‌ای با امکانات متوسط',
            'color' => 'gray',
            'is_popular' => true,
            'is_recommended' => true,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // ایجاد قیمت‌های پلن نقره‌ای
        PlanPrice::create([
            'plan_id' => $planSilver->id,
            'duration' => PlanDuration::SEMI_ANNUAL,
            'price' => 1500000,
            'is_active' => true,
        ]);

        // پلن طلایی - 1 ساله
        $planGold = Plan::create([
            'name' => 'طلایی',
            'slug' => 'gold',
            'description' => 'پلن طلایی با تمام امکانات',
            'color' => 'yellow',
            'is_popular' => false,
            'is_recommended' => false,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // ایجاد قیمت‌های پلن طلایی
        PlanPrice::create([
            'plan_id' => $planGold->id,
            'duration' => PlanDuration::ANNUAL,
            'price' => 4000000,
            'is_active' => true,
        ]);


    }
}

