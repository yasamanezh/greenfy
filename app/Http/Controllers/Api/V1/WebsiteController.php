<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\UserWebsite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebsiteController extends Controller
{

    /**
     * دریافت اعتبار کیف پول کاربر
     */
    public function getWalletBalance(Request $request, $domain = null): JsonResponse
    {
        try {
            // اگر domain ارسال شده، کیف پول صاحب سایت را برمی‌گردانیم
            if ($domain) {
                $website = UserWebsite::where('domain', $domain)
                    ->orWhere('subdomain', $domain)
                    ->with('user.wallet')
                    ->first();

                if (!$website) {
                    return response()->json([
                        'success' => false,
                        'message' => 'دامین مورد نظر یافت نشد',
                    ], 404);
                }

                $wallet = $website->user->wallet;

                if (!$wallet) {
                    $wallet = \App\Models\Wallet::getUserWallet($website->user);
                }

                $userInfo = [
                    'id' => $website->user->id,
                    'name' => $website->user->name,
                    'email' => $website->user->email,
                ];
            } else {
                // اگر کاربر لاگین کرده، کیف پول خودش را می‌گیریم
                if (!Auth::check()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'برای مشاهده کیف پول خود باید لاگین کنید',
                    ], 401);
                }

                $wallet = Auth::user()->wallet;

                if (!$wallet) {
                    $wallet = \App\Models\Wallet::getUserWallet(Auth::user());
                }

                $userInfo = [
                    'id' => Auth::id(),
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ];
            }

            $data = [
                'user' => $userInfo,
                'wallet' => [
                    'balance' => (float) $wallet->balance,
                    'formatted_balance' => $wallet->formatted_balance,
                    'total_earned' => (float) $wallet->total_earned,
                    'formatted_total_earned' => $wallet->formatted_total_earned,
                    'total_spent' => (float) $wallet->total_spent,
                    'formatted_total_spent' => $wallet->formatted_total_spent,
                    'balance_color' => $wallet->balance_color,
                ],
            ];

            return response()->json([
                'success' => true,
                'message' => 'اطلاعات کیف پول با موفقیت دریافت شد',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در دریافت اطلاعات کیف پول',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * دریافت تمام اطلاعات وب‌سایت به صورت یکپارچه
     */
    public function getWebsiteAllInfo($domain): JsonResponse
    {
        try {
            $website = UserWebsite::where('domain', $domain)
                ->orWhere('subdomain', $domain)
                ->with([
                    'user:id,name,email,phone',
                    'activeSubscription' => function($query) {
                        $query->with(['plan:id,name,slug', 'planPrice:id,duration,price']);
                    },
                    'smsCredits'
                ])
                ->first();

            if (!$website) {
                return response()->json([
                    'success' => false,
                    'message' => 'دامین مورد نظر یافت نشد',
                ], 404);
            }

            // دریافت اطلاعات کاربر و کیف پول
            $wallet = $website->user->wallet;
            if (!$wallet) {
                $wallet = \App\Models\Wallet::getUserWallet($website->user);
            }

            // آماده‌سازی اطلاعات اشتراک
            $subscriptionData = null;
            if ($website->activeSubscription) {
                $sub = $website->activeSubscription;
                $subscriptionData = [
                    'id' => $sub->id,
                    'status' => $sub->status->value,
                    'status_label' => $sub->status_label,
                    'start_date' => $sub->start_date->format('Y-m-d H:i:s'),
                    'start_date_persian' => $sub->start_date->format('Y/m/d H:i:s'),
                    'end_date' => $sub->end_date->format('Y-m-d H:i:s'),
                    'end_date_persian' => $sub->end_date->format('Y/m/d H:i:s'),
                    'remaining_days' => max(0, now()->diffInDays($sub->end_date, false)),
                    'is_active' => $sub->isActive(),
                    'plan' => [
                        'id' => $sub->plan->id,
                        'name' => $sub->plan->name,
                        'slug' => $sub->plan->slug,
                    ],
                    'plan_price' => $sub->planPrice ? [
                        'id' => $sub->planPrice->id,
                        'duration' => $sub->planPrice->duration->value,
                        'price' => (float) $sub->planPrice->price,
                    ] : null,
                ];
            }

            // آماده‌سازی اطلاعات اعتبار پیامک
            $smsCreditData = null;
            if ($website->smsCredits) {
                $smsCred = $website->smsCredits;
                $smsCreditData = [
                    'remaining_sms' => $smsCred->remaining_sms,
                    'formatted_remaining_sms' => $smsCred->formatted_remaining_sms,
                    'expires_at' => $smsCred->expires_at ? $smsCred->expires_at->format('Y-m-d H:i:s') : null,
                    'expires_at_formatted' => $smsCred->expires_at_formatted,
                    'remaining_days' => $smsCred->remaining_days,
                    'is_expired' => $smsCred->is_expired,
                    'is_unlimited' => $smsCred->is_unlimited,
                    'status' => $smsCred->status,
                    'status_label' => $smsCred->status_label,
                    'status_color' => $smsCred->status_color,
                ];
            }

            // آماده‌سازی اطلاعات کامل
            $data = [
                'website' => [
                    'id' => $website->id,
                    'name' => $website->name,
                    'domain' => $website->domain,
                    'subdomain' => $website->subdomain,
                    'description' => $website->description,
                    'theme' => $website->theme,
                    'status' => $website->status->value,
                    'status_label' => $website->status_label,
                ],
                'user' => [
                    'id' => $website->user->id,
                    'name' => $website->user->name,
                    'email' => $website->user->email,
                    'phone' => $website->user->phone,
                ],
                'subscription' => $subscriptionData,
                'sms_credit' => $smsCreditData,
                'wallet' => [
                    'balance' => (float) $wallet->balance,
                    'formatted_balance' => $wallet->formatted_balance,
                    'total_earned' => (float) $wallet->total_earned,
                    'formatted_total_earned' => $wallet->formatted_total_earned,
                    'total_spent' => (float) $wallet->total_spent,
                    'formatted_total_spent' => $wallet->formatted_total_spent,
                    'balance_color' => $wallet->balance_color,
                ],
            ];

            return response()->json([
                'success' => true,
                'message' => 'اطلاعات کامل وب‌سایت با موفقیت دریافت شد',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در دریافت اطلاعات وب‌سایت',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
