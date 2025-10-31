<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class VerifyOtpAction
{
    public function __invoke(string $phone, string $code): array
    {
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'code' => 'کاربر یافت نشد',
            ]);
        }

        if (!$user->verifyCode($code)) {
            throw ValidationException::withMessages([
                'code' => 'کد تایید نامعتبر است',
            ]);
        }

        $user->markPhoneAsVerified();
        $user->resetSmsAttempts();
        $user->sms_verification_code = null;
        $user->sms_verification_expires_at = null;
        $user->save();

        if ($user->registration_step === 'completed') {
            // پس از تایید موفق، کاربر را لاگین کن
            Auth::login($user);
            session()->regenerate();
            return [
                'completed' => true,
                'user' => $user,
                'nextStep' => null,
            ];
        }

        $nextStep = match ($user->registration_step) {
            'user-info', null, 'phone', 'verified' => 'user-info',
            'website-info' => 'website-info',
            default => 'user-info',
        };

        $user->setStep($nextStep);

        return [
            'completed' => false,
            'user' => $user,
            'nextStep' => $nextStep,
        ];
    }
}
