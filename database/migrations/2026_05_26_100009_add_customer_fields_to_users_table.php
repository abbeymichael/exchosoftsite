<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Customer-specific fields (if not already added)
            if (!Schema::hasColumn('users', 'account_type')) {
                $table->string('account_type')->default('customer')->after('email'); // admin, customer
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('account_type');
            }
            if (!Schema::hasColumn('users', 'company')) {
                $table->string('company')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'country')) {
                $table->string('country')->nullable()->after('company');
            }
            if (!Schema::hasColumn('users', 'is_customer')) {
                $table->boolean('is_customer')->default(false)->after('country');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['account_type', 'phone', 'company', 'country', 'is_customer']);
        });
    }
};
