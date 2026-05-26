<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // ── Main / Root Admin ─────────────────────────────────────────────────
        // is_main_admin = true means this account can NEVER be deleted or
        // demoted, and is_active is always forced to true.
        // Credentials come from .env (MAIN_ADMIN_EMAIL / MAIN_ADMIN_PASSWORD)
        // and fall back to the defaults below for development only.
        // ─────────────────────────────────────────────────────────────────────

        $mainEmail    = env('MAIN_ADMIN_EMAIL', 'admin@exchosoft.com');
        $mainPassword = env('MAIN_ADMIN_PASSWORD', 'Exchosoft@2024!');
        $mainName     = env('MAIN_ADMIN_NAME', 'ExchoSoft Admin');

        $main = User::updateOrCreate(
            ['email' => $mainEmail],
            [
                'name'              => $mainName,
                'email'             => $mainEmail,
                'password'          => Hash::make($mainPassword),
                'role'              => 'super_admin',
                'is_main_admin'     => true,
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info("  Main admin seeded → {$mainEmail}");
        $this->command->warn('  !! Change the default password in production !!');

        // ── Optional: seed a secondary super admin for dev/testing ───────────
        if (app()->environment('local', 'development', 'staging')) {
            User::updateOrCreate(
                ['email' => 'dev@exchosoft.com'],
                [
                    'name'              => 'Dev Admin',
                    'email'             => 'dev@exchosoft.com',
                    'password'          => Hash::make('devpassword'),
                    'role'              => 'super_admin',
                    'is_main_admin'     => false,
                    'is_active'         => true,
                    'email_verified_at' => now(),
                    'created_by'        => $main->id,
                ]
            );

            $this->command->info('  Dev admin seeded → dev@exchosoft.com / devpassword');
        }
    }
}
