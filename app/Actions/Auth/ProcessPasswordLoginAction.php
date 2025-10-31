<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProcessPasswordLoginAction
{
    public function __invoke(string $phone, string $password): void
    {
        $user = User::where('phone', $phone)->first();

        if (!$user || !$user->password || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'کلمه عبور وارد شده صحیح نیست',
            ]);
        }

        Auth::login($user);
    }
}
