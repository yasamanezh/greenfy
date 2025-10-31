<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Library\SMS;
use Livewire\Component;

class ForgotPassword extends Component
{
    public $phone = '';
    public $verificationCode = '';
    public $newPassword = '';
    public $confirmPassword = '';
    public $step = 'phone'; // phone, verify, reset
    public $message = '';
    public $messageType = '';
    
    public function getIllustrationProperty()
    {
        return 'auth-two-steps-illustration.png';
    }

    public function sendCode()
    {
        $this->validate([
            'phone' => 'required|numeric|digits:11|regex:/^09[0-9]{9}$/',
        ], [
            'phone.required' => 'شماره تلفن الزامی است',
            'phone.regex' => 'شماره تلفن معتبر نیست',
        ]);

        $user = User::where('phone', $this->phone)->first();

        if (!$user) {
            $this->message = 'شماره تلفن یافت نشد';
            $this->messageType = 'danger';
            return;
        }

        // بررسی بن بودن
        if ($user->isBanned()) {
            $this->message = 'شما برای 2 ساعت بن شدید. لطفاً بعداً تلاش کنید.';
            $this->messageType = 'danger';
            return;
        }

        // بررسی محدودیت ارسال پیامک
        if (!$user->canSendSms()) {
            $this->message = 'شما بیش از حد مجاز درخواست کد کرده‌اید. لطفاً بعداً تلاش کنید.';
            $this->messageType = 'danger';
            return;
        }

        // ایجاد کد تایید
        $user->generateVerificationCode();
        $user->incrementSmsAttempts();

        // ارسال پیامک
        $result = SMS::sendVerify($this->phone, $user->sms_verification_code);
        
        if ($result) {
            $this->step = 'verify';
            $this->message = 'کد تایید به شماره شما ارسال شد';
            $this->messageType = 'success';
        } else {
            $this->message = 'خطا در ارسال پیامک. لطفاً دوباره تلاش کنید.';
            $this->messageType = 'danger';
        }
    }

    public function verifyCode()
    {
        $this->validate([
            'verificationCode' => 'required|numeric|digits:5',
        ]);

        $user = User::where('phone', $this->phone)->first();

        if ($user && $user->verifyCode($this->verificationCode)) {
            $this->step = 'reset';
            $this->message = '';
        } else {
            $this->message = 'کد تایید نامعتبر است';
            $this->messageType = 'danger';
        }
    }

    public function resetPassword()
    {
        $this->validate([
            'newPassword' => 'required|string|min:8',
            'confirmPassword' => 'required|same:newPassword',
        ], [
            'newPassword.required' => 'رمز عبور الزامی است',
            'newPassword.min' => 'رمز عبور باید حداقل 8 کاراکتر باشد',
            'confirmPassword.required' => 'تایید رمز عبور الزامی است',
            'confirmPassword.same' => 'رمزهای عبور مطابقت ندارند',
        ]);

        $user = User::where('phone', $this->phone)->first();

        if ($user) {
            $user->update([
                'password' => bcrypt($this->newPassword),
                'sms_verification_code' => null,
                'sms_verification_expires_at' => null,
            ]);
            $user->resetSmsAttempts();

            $this->message = 'رمز عبور شما با موفقیت تغییر کرد';
            $this->messageType = 'success';

            // هدایت به صفحه لاگین
            return $this->redirect(route('login'), navigate: false);
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password')
            ->layout('layouts.auth', [
                'illustration' => $this->illustration
            ]);
    }
}
