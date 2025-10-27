<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            FeatureSeeder::class,
            PlanSeeder::class,
            ModuleSeeder::class,
            SmsPackageSeeder::class,
            WebsiteSeeder::class,
        ]);
    }
}
