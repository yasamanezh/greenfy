<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WebsiteSubscription;
use App\Models\WebsiteModule;
use App\Models\WebsiteSmsTransaction;
use App\Models\UserSmsCredit;
use App\Models\PlanUpgrade;
use App\Models\WebsiteSmsCredit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;

class PaymentCallbackController extends Controller
{
    /**
     * هندل کردن کال‌بک پرداخت
     */
    public function handleCallback(Request $request, $gateway)
    {
        // لاگ درخواست برای دیباگ
        Log::info('Payment Callback Received', [
            'gateway' => $gateway,
            'request_data' => $request->all(),
            'ip' => $request->ip()
        ]);

        try {
            // اعتبارسنجی درگاه پرداخت
            $validationResult = $this->validateGateway($gateway, $request);
            if (!$validationResult['success']) {
                return $this->failedResponse($validationResult['message']);
            }

            // پیدا کردن تراکنش بر اساس توکن
            $transaction = $this->findTransaction($this->getPaymentToken($gateway, $request));
            if (!$transaction) {
                return $this->failedResponse('تراکنش یافت نشد');
            }

            // اگر تراکنش قبلاً پرداخت شده
            if ($transaction->status === 'completed') {
                return $this->successResponse($transaction, 'تراکنش قبلاً با موفقیت پرداخت شده است');
            }

            // تأیید پرداخت با درگاه
            $this->verifyPayment($gateway, $request);

            // پردازش تراکنش در تراکنش دیتابیس
            return DB::transaction(function () use ($transaction, $request, $gateway) {
                // بروزرسانی وضعیت تراکنش
                $transaction->update([
                    'status' => TransactionStatus::COMPLETED->value,
                    'gateway_response' => $request->all()
                ]);

                // پردازش بر اساس نوع سرویس خریداری شده
                $result = $this->processPaymentBasedOnType($transaction);

                if (!$result['success']) {
                    // بازگشت به حالت قبلی در صورت خطا
                    DB::rollBack();
                    return $this->failedResponse($result['message']);
                }

                // اگر از کیف پول استفاده شده، از کیف پول کم کنیم
                if ($transaction->wallet_used && $transaction->wallet_amount > 0) {
                    $walletDeductionResult = $this->deductFromWallet($transaction);
                    if (!$walletDeductionResult['success']) {
                        DB::rollBack();
                        return $this->failedResponse($walletDeductionResult['message']);
                    }
                }

                Log::info('Payment Processed Successfully', [
                    'transaction_id' => $transaction->id,
                    'user_id' => $transaction->user_id,
                    'amount' => $transaction->amount,
                    'gateway' => $gateway,
                    'type' => $transaction->transactionable_type
                ]);

                return $this->successResponse($transaction, 'پرداخت با موفقیت انجام شد');
            });
        } catch (InvalidPaymentException $e) {
            Log::error('Invalid Payment Exception', [
                'gateway' => $gateway,
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return $this->failedResponse('پرداخت نامعتبر است: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Payment Callback Error', [
                'gateway' => $gateway,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return $this->failedResponse('خطا در پردازش پرداخت: ' . $e->getMessage());
        }
    }

    /**
     * اعتبارسنجی درگاه پرداخت
     */
    private function validateGateway($gateway, $request)
    {
        $validators = [
            'zarinpal' => function ($request) {
                return isset($request->Authority) && isset($request->Status);
            },
            'idpay' => function ($request) {
                return isset($request->id) && isset($request->order_id);
            },
            'parsian' => function ($request) {
                return isset($request->Token) && isset($request->status);
            },
            'saman' => function ($request) {
                return isset($request->RefNum) && isset($request->State);
            }
        ];

        if (!isset($validators[$gateway])) {
            return ['success' => false, 'message' => 'درگاه پرداخت نامعتبر است'];
        }

        $isValid = $validators[$gateway]($request);

        if (!$isValid) {
            return ['success' => false, 'message' => 'داده‌های درگاه پرداخت نامعتبر است'];
        }

        // بررسی وضعیت پرداخت
        $statusCheck = $this->checkPaymentStatus($gateway, $request);
        if (!$statusCheck['success']) {
            return $statusCheck;
        }

        return ['success' => true];
    }

    /**
     * بررسی وضعیت پرداخت
     */
    private function checkPaymentStatus($gateway, $request)
    {
        $statusChecks = [
            'zarinpal' => function ($request) {
                return $request->Status == 'OK';
            },
            'idpay' => function ($request) {
                return $request->status == 100;
            },
            'parsian' => function ($request) {
                return $request->status == 0;
            },
            'saman' => function ($request) {
                return $request->State == 'OK';
            }
        ];

        $isSuccess = $statusChecks[$gateway]($request);

        if (!$isSuccess) {
            return [
                'success' => false,
                'message' => 'پرداخت ناموفق بود یا توسط کاربر لغو شد'
            ];
        }

        return ['success' => true];
    }

    /**
     * دریافت توکن پرداخت بر اساس درگاه
     */
    private function getPaymentToken($gateway, $request)
    {
        return match ($gateway) {
            'zarinpal' => $request->Authority,
            'idpay' => $request->id,
            'parsian' => $request->Token,
            'saman' => $request->RefNum,
            default => $request->payment_token
        };
    }

    /**
     * تأیید پرداخت با درگاه
     */
    private function verifyPayment($gateway, $request)
    {
        $amount = $this->getPaymentAmount($gateway, $request);
        $transactionId = $this->getPaymentToken($gateway, $request);

        // تأیید پرداخت با درگاه
        Payment::via($gateway)
            ->amount($amount)
            ->transactionId($transactionId)
            ->verify();
    }

    /**
     * دریافت مبلغ پرداخت بر اساس درگاه
     */
    private function getPaymentAmount($gateway, $request)
    {
        return match ($gateway) {
            'zarinpal' => $request->Amount,
            'idpay' => $request->amount,
            'parsian' => $request->Amount,
            'saman' => $request->Amount,
            default => $request->amount
        };
    }

    /**
     * پیدا کردن تراکنش
     */
    private function findTransaction($paymentToken)
    {
        return Transaction::where('payment_token', $paymentToken)
            ->orWhere('id', $paymentToken)
            ->first();
    }

    /**
     * پردازش بر اساس نوع سرویس خریداری شده
     */
    private function processPaymentBasedOnType(Transaction $transaction)
    {
        $transactionable = $transaction->transactionable;

        if (!$transactionable) {
            return ['success' => false, 'message' => 'سرویس خریداری شده یافت نشد'];
        }

        $processor = match ($transaction->transactionable_type) {
            'App\Models\WebsiteSubscription'   => 'processWebsiteSubscription',
            'App\Models\WebsiteModule'         => 'processWebsiteModule',
            'App\Models\WebsiteSmsTransaction' => 'processSmsPackage',
            'App\Models\PlanUpgrade'           => 'processPlanUpgrade',
            'App\Models\Wallet'                => 'processWalletDeposit',
            default => null
        };

        if (!$processor) {
            return ['success' => false, 'message' => 'نوع تراکنش پشتیبانی نمی‌شود'];
        }

        return $this->$processor($transaction, $transactionable);
    }

    /**
     * پردازش خرید پلن سایت
     */
    private function processWebsiteSubscription(Transaction $transaction, $subscription)
    {
        try {
            // فعال کردن اشتراک سایت
            $subscription->update([
                'status' => 'active',
                'start_date' => now(),
                'end_date' => now()->addDays($subscription->planPrice->getDurationDays()),
            ]);

            // غیرفعال کردن اشتراک‌های قبلی این سایت
            WebsiteSubscription::where('user_website_id', $subscription->user_website_id)
                ->where('id', '!=', $subscription->id)
                ->where('status', 'active')
                ->update(['status' => 'expired']);

            Log::info('Website Subscription Activated', [
                'subscription_id' => $subscription->id,
                'user_website_id' => $subscription->user_website_id,
                'plan_id' => $subscription->plan_id,
                'duration_days' => $subscription->planPrice->getDurationDays()
            ]);

            return ['success' => true, 'message' => 'اشتراک سایت با موفقیت فعال شد'];
        } catch (\Exception $e) {
            Log::error('Website Subscription Activation Failed', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return ['success' => false, 'message' => 'خطا در فعال‌سازی اشتراک سایت'];
        }
    }

    /**
     * پردازش خرید ماژول برای سایت
     */
    private function processWebsiteModule(Transaction $transaction, $websiteModule)
    {
        try {
            // فعال کردن ماژول برای سایت
            $websiteModule->update([
                'status' => 'active',
                'start_date' => now(),
                'end_date' => now()->addDays($websiteModule->module->validity_days),
            ]);
            return ['success' => true, 'message' => 'ماژول با موفقیت برای سایت فعال شد'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'خطا در فعال‌سازی ماژول'];
        }
    }

    /**
     * پردازش خرید پکیج پیامک
     */
    /**
     * پردازش خرید پکیج پیامک
     */
    private function processSmsPackage(Transaction $transaction, $smsTransaction)
    {
        try {
            // تکمیل تراکنش پیامک
            $smsTransaction->update([
                'status' => 'completed'
            ]);

            // افزایش اعتبار پیامک سایت - استفاده از WebsiteSmsCredit
            $websiteSmsCredit = WebsiteSmsCredit::firstOrCreate(
                ['user_website_id' => $smsTransaction->user_website_id],
                ['remaining_sms' => 0, 'expires_at' => null]
            );

            $oldSmsCount = $websiteSmsCredit->remaining_sms;
            $websiteSmsCredit->increment('remaining_sms', $smsTransaction->sms_count);

            // بروزرسانی تاریخ انقضا
            if (!$websiteSmsCredit->expires_at || $smsTransaction->expires_at->gt($websiteSmsCredit->expires_at)) {
                $websiteSmsCredit->update(['expires_at' => $smsTransaction->expires_at]);
            }

            return ['success' => true, 'message' => 'پکیج پیامک با موفقیت فعال شد'];
        } catch (\Exception $e) {
            Log::error('SMS Package Activation Failed', [
                'sms_transaction_id' => $smsTransaction->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return ['success' => false, 'message' => 'خطا در فعال‌سازی پکیج پیامک'];
        }
    }

    /**
     * پردازش ارتقای پلن
     */
    private function processPlanUpgrade(Transaction $transaction, $planUpgrade)
    {
        try {
            // تکمیل درخواست ارتقا
            $planUpgrade->update([
                'status' => 'completed',
                'paid_amount' => $transaction->amount
            ]);

            // ایجاد اشتراک جدید
            $newSubscription = WebsiteSubscription::create([
                'user_website_id' => $planUpgrade->user_website_id,
                'plan_id' => $planUpgrade->to_plan_id,
                'plan_price_id' => $planUpgrade->to_plan_price_id,
                'start_date' => now(),
                'end_date' => now()->addDays($planUpgrade->toPlanPrice->getDurationDays()),
                'status' => 'active'
            ]);

            // غیرفعال کردن اشتراک قبلی
            WebsiteSubscription::where('id', $planUpgrade->from_website_subscription_id)
                ->update(['status' => 'expired']);

            Log::info('Plan Upgrade Completed', [
                'plan_upgrade_id' => $planUpgrade->id,
                'from_plan_id' => $planUpgrade->from_plan_id,
                'to_plan_id' => $planUpgrade->to_plan_id,
                'new_subscription_id' => $newSubscription->id
            ]);

            return ['success' => true, 'message' => 'ارتقای پلن با موفقیت انجام شد'];
        } catch (\Exception $e) {
            Log::error('Plan Upgrade Failed', [
                'plan_upgrade_id' => $planUpgrade->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return ['success' => false, 'message' => 'خطا در ارتقای پلن'];
        }
    }

    /**
     * پردازش شارژ کیف پول
     */
    private function processWalletDeposit(Transaction $transaction, $wallet)
    {
        try {
            // افزایش موجودی کیف پول
            $wallet->increment('balance', $transaction->amount);
            $wallet->increment('total_earned', $transaction->amount);

            // ثبت در تاریخچه کیف پول
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'deposit',
                'amount' => $transaction->amount,
                'balance_after' => $wallet->balance + $transaction->amount,
                'description' => 'شارژ کیف پول از طریق درگاه پرداخت',
                'reference_type' => 'Transaction',
                'reference_id' => $transaction->id,
                'status' => 'completed'
            ]);

            Log::info('Wallet Deposit Completed', [
                'wallet_id' => $wallet->id,
                'user_id' => $wallet->user_id,
                'amount' => $transaction->amount,
                'new_balance' => $wallet->balance
            ]);

            return ['success' => true, 'message' => 'کیف پول با موفقیت شارژ شد'];
        } catch (\Exception $e) {
            Log::error('Wallet Deposit Failed', [
                'wallet_id' => $wallet->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return ['success' => false, 'message' => 'خطا در شارژ کیف پول'];
        }
    }

    /**
     * کسر از کیف پول
     */
    private function deductFromWallet(Transaction $transaction)
    {
        try {
            $wallet = Wallet::where('user_id', $transaction->user_id)->first();

            if (!$wallet) {
                return ['success' => false, 'message' => 'کیف پول کاربر یافت نشد'];
            }

            if ($wallet->balance < $transaction->wallet_amount) {
                return ['success' => false, 'message' => 'موجودی کیف پول کافی نیست'];
            }

            $wallet->decrement('balance', $transaction->wallet_amount);
            $wallet->increment('total_spent', $transaction->wallet_amount);

            // ثبت در تاریخچه کیف پول
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_website_id' => $transaction->user_website_id,
                'type' => 'purchase',
                'amount' => -$transaction->wallet_amount,
                'balance_after' => $wallet->balance - $transaction->wallet_amount,
                'description' => 'خرید از طریق کیف پول',
                'reference_type' => 'Transaction',
                'reference_id' => $transaction->id,
                'status' => 'completed'
            ]);

            Log::info('Wallet Deduction Completed', [
                'wallet_id' => $wallet->id,
                'user_id' => $transaction->user_id,
                'amount' => $transaction->wallet_amount,
                'new_balance' => $wallet->balance
            ]);

            return ['success' => true, 'message' => 'مبلغ از کیف پول کسر شد'];
        } catch (\Exception $e) {
            Log::error('Wallet Deduction Failed', [
                'user_id' => $transaction->user_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return ['success' => false, 'message' => 'خطا در کسر از کیف پول'];
        }
    }

    /**
     * پاسخ موفق
     */
    private function successResponse(Transaction $transaction, $message = '')
    {
        $redirectUrl = $this->getSuccessRedirectUrl($transaction);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'transaction' => [
                    'id' => $transaction->id,
                    'amount' => $transaction->amount,
                    'gateway' => $transaction->gateway,
                    'type' => $transaction->transactionable_type
                ],
                'redirect_url' => $redirectUrl
            ]
        ]);
    }

    /**
     * پاسخ خطا
     */
    private function failedResponse($message)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => [
                'redirect_url' => url('/payment/failed')
            ]
        ], 400);
    }

    /**
     * تعیین مسیر ریدایرکت پس از پرداخت موفق
     */
    private function getSuccessRedirectUrl(Transaction $transaction)
    {
        $baseUrls = [
            'App\Models\WebsiteSubscription' => '/websites?payment=success',
            'App\Models\WebsiteModule' => '/websites/modules?payment=success',
            'App\Models\WebsiteSmsTransaction' => '/websites/sms?payment=success',
            'App\Models\PlanUpgrade' => '/websites/subscriptions?payment=success',
            'App\Models\Wallet' => '/wallet?payment=success',
        ];

        $baseUrl = $baseUrls[$transaction->transactionable_type] ?? '/dashboard?payment=success';

        return url($baseUrl . '&transaction_id=' . $transaction->id);
    }
}
