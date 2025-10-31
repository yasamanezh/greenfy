<?php

namespace App\Actions\Auth;

use App\Library\SMS;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SendOtpAction
{
    public function __invoke(string $phone, bool $forceOtp = false): array
    {
        $user = User::where('phone', $phone)->first();

        if ($user && $user->password && !$forceOtp) {
            return [
                'mode' => 'password',
                'user' => $user,
            ];
        }

        if (!$user) {
            $user = User::create([
                'phone' => $phone,
                'name' => null,
                'email' => null,
                'password' => null,
                'registration_step' => 'user-info',
            ]);
        }

        if ($user->isBanned()) {
            throw ValidationException::withMessages([
                'phone' => 'به دلیل ارسال بیش از حد درخواست، حساب شما موقتاً مسدود شده است. لطفاً بعداً تلاش کنید.',
            ]);
        }

        if (!$user->canSendSms()) {
            throw ValidationException::withMessages([
                'phone' => 'درخواست‌های ارسال کد بیش از حد مجاز است. لطفاً دقایقی دیگر تلاش کنید.',
            ]);
        }

        $user->generateVerificationCode();
        $user->incrementSmsAttempts();

        // SMS::sendVerify($phone, $user->sms_verification_code);

        return [
            'mode' => 'otp',
            'user' => $user,
        ];
    }
}
