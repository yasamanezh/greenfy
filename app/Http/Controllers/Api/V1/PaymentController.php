<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\WebsiteSubscription;
use App\Models\WebsiteModule;
use App\Models\WebsiteSmsTransaction;
use App\Models\PlanUpgrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

class PaymentController extends Controller
{
    /**
     * ارسال کاربر به درگاه پرداخت
     */
    public function redirectToGateway(Request $request)
    {
        // اعتبارسنجی داده‌های ورودی
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:subscription,module,sms,upgrade,wallet',
            'id' => 'required|integer',
            'use_wallet' => 'boolean',
            'gateway' => 'required|in:zarinpal,idpay,parsian,saman'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'داده‌های ورودی نامعتبر است',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // ایجاد تراکنش بر اساس نوع
            $transaction = $this->createTransaction(
                $request->type, 
                $request->id, 
                $request->use_wallet, 
                $request->gateway
            );
            
            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'آیتم مورد نظر یافت نشد'
                ], 404);
            }

            // اگر از کیف پول استفاده شده و موجودی کافی است
            if ($request->use_wallet && $transaction->wallet_amount > 0) {
                $wallet = Wallet::where('user_id', auth()->id())->first();
                
                if (!$wallet || $wallet->balance < $transaction->wallet_amount) {
                    return response()->json([
                        'success' => false,
                        'message' => 'موجودی کیف پول شما کافی نیست'
                    ], 400);
                }
            }

            // ایجاد invoice برای درگاه پرداخت
            $invoice = (new Invoice)
                ->amount($transaction->amount)
                ->detail([
                    'description' => $this->getPaymentDescription($transaction),
                    'email' => auth()->user()->email,
                    'mobile' => auth()->user()->phone,
                ]);

            // ایجاد لینک پرداخت
            $payment = Payment::via($request->gateway)->purchase(
                $invoice,
                function($driver, $transactionId) use ($transaction, $request) {
                    // بروزرسانی تراکنش با transactionId درگاه
                    $transaction->update([
                        'payment_token' => $transactionId,
                        'gateway' => $request->gateway
                    ]);
                }
            );

            DB::commit();

            // بازگشت لینک پرداخت به فرانت‌اند
            return response()->json([
                'success' => true,
                'message' => 'لینک پرداخت ایجاد شد',
                'data' => [
                    'payment_url' => $payment->pay()->getAction(),
                    'transaction_id' => $transaction->id,
                    'amount' => $transaction->amount,
                    'gateway' => $request->gateway
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Payment Redirect Error', [
                'error' => $e->getMessage(),
                'type' => $request->type,
                'id' => $request->id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'خطا در اتصال به درگاه پرداخت'
            ], 500);
        }
    }

    /**
     * ایجاد تراکنش بر اساس نوع
     */
    private function createTransaction($type, $id, $useWallet = false, $gateway = 'zarinpal')
    {
        $user = auth()->user();
        $amount = 0;
        $walletAmount = 0;
        $transactionable = null;
        $userWebsiteId = null;

        switch ($type) {
            case 'subscription':
                $subscription = WebsiteSubscription::find($id);
                if (!$subscription || $subscription->user_website->user_id != $user->id) {
                    return null;
                }
                $amount = $subscription->planPrice->price;
                $transactionable = $subscription;
                $userWebsiteId = $subscription->user_website_id;
                break;

            case 'module':
                $websiteModule = WebsiteModule::find($id);
                if (!$websiteModule || $websiteModule->user_website->user_id != $user->id) {
                    return null;
                }
                $amount = $websiteModule->module->getFinalPriceAttribute();
                $transactionable = $websiteModule;
                $userWebsiteId = $websiteModule->user_website_id;
                break;

            case 'sms':
                $smsTransaction = WebsiteSmsTransaction::find($id);
                if (!$smsTransaction || $smsTransaction->user_website->user_id != $user->id) {
                    return null;
                }
                $amount = $smsTransaction->sms_package->getFinalPriceAttribute();
                $transactionable = $smsTransaction;
                $userWebsiteId = $smsTransaction->user_website_id;
                break;

            case 'upgrade':
                $planUpgrade = PlanUpgrade::find($id);
                if (!$planUpgrade || $planUpgrade->user_id != $user->id) {
                    return null;
                }
                $amount = $planUpgrade->upgrade_price;
                $transactionable = $planUpgrade;
                $userWebsiteId = $planUpgrade->user_website_id;
                break;

            case 'wallet':
                $amount = $id; // در این حالت id مقدار شارژ است
                $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);
                $transactionable = $wallet;
                break;
        }

        // اگر از کیف پول استفاده شود
        if ($useWallet && $type != 'wallet') {
            $wallet = Wallet::where('user_id', $user->id)->first();
            if ($wallet) {
                $walletAmount = min($wallet->balance, $amount);
                $amount -= $walletAmount;
            }
        }

        // ایجاد تراکنش
        return Transaction::create([
            'user_id' => $user->id,
            'user_website_id' => $userWebsiteId,
            'transactionable_type' => get_class($transactionable),
            'transactionable_id' => $transactionable->id,
            'amount' => $amount,
            'wallet_used' => $useWallet,
            'wallet_amount' => $walletAmount,
            'gateway' => $gateway,
            'status' => 'pending'
        ]);
    }

    /**
     * دریافت توضیحات پرداخت
     */
    private function getPaymentDescription(Transaction $transaction)
    {
        return match ($transaction->transactionable_type) {
            'App\Models\WebsiteSubscription' => 'خرید اشتراک سایت',
            'App\Models\WebsiteModule' => 'خرید ماژول',
            'App\Models\WebsiteSmsTransaction' => 'خرید پکیج پیامک',
            'App\Models\PlanUpgrade' => 'ارتقای پلن',
            'App\Models\Wallet' => 'شارژ کیف پول',
            default => 'پرداخت آنلاین'
        };
    }
}