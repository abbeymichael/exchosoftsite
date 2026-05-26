<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tracks whether an activation came from a desktop, web, cloud, or hybrid app.
 * Advisory only — does not change licensing logic.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('license_activations', function (Blueprint $table) {
            $table->string('app_type', 20)->default('desktop')
                ->after('platform')
                ->comment('desktop|web|cloud|hybrid');
        });
    }

    public function down(): void
    {
        Schema::table('license_activations', function (Blueprint $table) {
            $table->dropColumn('app_type');
        });
    }
};
