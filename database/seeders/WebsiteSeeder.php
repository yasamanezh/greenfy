<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserWebsite;
use App\Models\WebsiteSubscription;
use App\Models\WebsiteSmsCredit;
use App\Models\Plan;
use App\Models\PlanPrice;
use App\Enums\WebsiteStatus;
use App\Enums\SubscriptionStatus;
use Illuminate\Database\Seeder;

class WebsiteSeeder extends Seeder
{
    public function run(): void
    {
        // دریافت کاربران
        $users = User::get();
        
        // دریافت پلن‌ها
        $planBasic = Plan::where('slug', 'basic')->first();
        $planSilver = Plan::where('slug', 'silver')->first();
        $planGold = Plan::where('slug', 'gold')->first();

        // برای هر کاربر 2 سایت ایجاد می‌کنیم
        foreach ($users as $index => $user) {
            // سایت اول - با اشتراک عادی
            $website1 = UserWebsite::create([
                'user_id' => $user->id,
                'name' => 'سایت ' . $user->name,
                'domain' => 'site-' . $user->id . '-1.example.com',
                'subdomain' => 'site-' . $user->id . '-1',
                'description' => 'سایت اول کاربر',
                'theme' => 'default',
                'status' => WebsiteStatus::ACTIVE,
            ]);

            // اشتراک فعال با پلن عادی
            if ($planBasic && $planBasic->prices->isNotEmpty()) {
                WebsiteSubscription::create([
                    'user_website_id' => $website1->id,
                    'plan_id' => $planBasic->id,
                    'plan_price_id' => $planBasic->prices->first()->id,
                    'start_date' => now()->subMonth(),
                    'end_date' => now()->addMonths(2),
                    'status' => SubscriptionStatus::ACTIVE,
                ]);
            }

            // اعتبار پیامک
            WebsiteSmsCredit::create([
                'user_website_id' => $website1->id,
                'remaining_sms' => 100,
                'expires_at' => now()->addMonths(1),
            ]);

            // سایت دوم - با اشتراک نقره‌ای
            $website2 = UserWebsite::create([
                'user_id' => $user->id,
                'name' => 'سایت دوم ' . $user->name,
                'domain' => 'site-' . $user->id . '-2.example.com',
                'subdomain' => 'site-' . $user->id . '-2',
                'description' => 'سایت دوم کاربر',
                'theme' => 'premium',
                'status' => $index === 0 ? WebsiteStatus::ACTIVE : WebsiteStatus::BUILDING,
            ]);

            // اشتراک با پلن نقره‌ای
            if ($planSilver && $planSilver->prices->isNotEmpty()) {
                WebsiteSubscription::create([
                    'user_website_id' => $website2->id,
                    'plan_id' => $planSilver->id,
                    'plan_price_id' => $planSilver->prices->first()->id,
                    'start_date' => now()->subMonths(2),
                    'end_date' => now()->addMonths(4),
                    'status' => SubscriptionStatus::ACTIVE,
                ]);
            }

            // اعتبار پیامک بیشتر برای سایت دوم
            WebsiteSmsCredit::create([
                'user_website_id' => $website2->id,
                'remaining_sms' => 500,
                'expires_at' => now()->addMonths(3),
            ]);
        }

      
    }
}

