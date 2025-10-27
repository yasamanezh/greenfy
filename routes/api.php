<?php

use App\Http\Controllers\Api\V1\PaymentCallbackController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\PlanController;
use App\Http\Controllers\Api\V1\SmsPackageController;
use App\Http\Controllers\Api\V1\WebsiteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('/payment/callback/{gateway}', [PaymentCallbackController::class, 'handleCallback'])->name('api.payment.callback');
Route::get('/payment', [PaymentController::class, 'redirectToGateway'])->name('api.payment');

// API های عمومی (بدون نیاز به لاگین)
Route::get('/plans', [PlanController::class, 'index'])->name('api.plans.index');
Route::get('/sms-packages', [SmsPackageController::class, 'index'])->name('api.sms-packages.index');

Route::get('/websites/{domain}', [WebsiteController::class, 'getWebsiteAllInfo'])->name('api.websites.show');
Route::get('/wallets/balance/{domain?}', [WebsiteController::class, 'getWalletBalance'])->name('api.wallets.balance');

