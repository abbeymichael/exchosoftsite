<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Run with:  php artisan db:seed
     * Fresh run: php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            ProductSeeder::class,
            CustomerSeeder::class,
            LicenseSeeder::class,
            ActivationSeeder::class,
            PageSeeder::class,
        ]);
    }
}
