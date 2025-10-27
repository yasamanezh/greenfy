<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'admin',
                'email' => 'admin@test.com',
                'phone' => '09123456789',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'کاربر تست 1',
                'email' => 'user1@test.com',
                'phone' => '09111111111',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'کاربر تست 2',
                'email' => 'user2@test.com',
                'phone' => '09222222222',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $index => $userData) {
            $user = User::create($userData);
            

            Wallet::create([
                'user_id' => $user->id,
                'balance' => 1000000,
                'total_earned' =>  0 ,
                'total_spent' =>  0 ,
            ]);
        }
    }
}

