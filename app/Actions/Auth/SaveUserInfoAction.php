<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SaveUserInfoAction
{
    public function __invoke(string $phone, array $data): void
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|min:3',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ], [
            'name.required' => 'نام و نام خانوادگی الزامی است',
            'name.min' => 'نام باید حداقل 3 کاراکتر باشد',
            'email.required' => 'ایمیل الزامی است',
            'email.email' => 'لطفاً یک ایمیل معتبر وارد کنید',
            'password.required' => 'کلمه عبور الزامی است',
            'password.min' => 'کلمه عبور باید حداقل 8 کاراکتر باشد',
            'password.confirmed' => 'تکرار کلمه عبور با کلمه عبور مطابقت ندارد',
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

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->setStep('website-info');
    }
}
