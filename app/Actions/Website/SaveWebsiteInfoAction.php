<?php

namespace App\Actions\Website;

use App\Enums\SubscriptionStatus;
use App\Enums\WebsiteStatus;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserWebsite;
use App\Models\Wallet;
use App\Models\WebsiteSubscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SaveWebsiteInfoAction
{
    public function __invoke(string $phone, array $data): void
    {
        $validator = Validator::make($data, [
            'websiteName' => 'required|string|min:3',
            'subdomain' => 'required|string|min:3|regex:/^[a-z0-9-]+$/|unique:user_websites,subdomain',
            'description' => 'nullable|string',
        ], [
            'websiteName.required' => 'نام وبسایت را وارد کنید',
            'websiteName.min' => 'نام وبسایت باید حداقل 3 کاراکتر باشد',
            'subdomain.required' => 'زیردامنه را وارد کنید',
            'subdomain.regex' => 'زیردامنه فقط می‌تواند شامل حروف کوچک، اعداد و خط تیره باشد',
            'subdomain.unique' => 'این زیردامنه قبلاً استفاده شده است',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = User::where('phone', $phone)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'phone' => 'کاربر یافت نشد',
            ]);
        }

        DB::transaction(function () use ($user, $data) {
            $website = UserWebsite::create([
                'user_id' => $user->id,
                'name' => $data['websiteName'],
                'subdomain' => $data['subdomain'],
                'description' => $data['description'] ?? null,
                'status' => WebsiteStatus::ACTIVE,
                'theme' => 'default',
            ]);

            $wallet = Wallet::getUserWallet($user);
            $wallet->deposit(50000, 'موجودی اولیه کیف پول');

            $plan = Plan::active()->orderBy('sort_order')->first();

            if ($plan) {
                $planPrice = $plan->prices()->where('is_active', true)->first();

                if ($planPrice) {
                    WebsiteSubscription::create([
                        'user_website_id' => $website->id,
                        'plan_id' => $plan->id,
                        'plan_price_id' => $planPrice->id,
                        'start_date' => now(),
                        'end_date' => now()->addDays(7),
                        'status' => SubscriptionStatus::ACTIVE,
                    ]);
                }
            }

            $user->setStep('completed');
        });

        // اطمینان از ورود کاربر پس از تکمیل ساخت وبسایت
        Auth::login($user);
        session()->regenerate();
    }
}
