<?php

use App\Livewire\Front\Home\Index;
use App\Livewire\Front\Payment\Callback;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ForgotPassword;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', Index::class)->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    // استفاده از کامپوننت‌های Livewire به صورت مستقیم
    Route::get('/login/{step?}', Register::class)->name('login');
    Route::get('/register/{step?}', Register::class)->name('register');
    Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
});


// Protected Routes
Route::middleware('auth')->post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', \App\Livewire\Admin\Dashboard\Index::class)->name('dashboard');
});
