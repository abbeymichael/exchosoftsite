<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('admin')
                ->comment('super_admin|admin')->after('email');
            $table->boolean('is_main_admin')->default(false)
                ->comment('The main/root admin — cannot be deleted or demoted')->after('role');
            $table->string('avatar')->nullable()->after('is_main_admin');
            $table->timestamp('last_login_at')->nullable()->after('avatar');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            $table->boolean('is_active')->default(true)->after('last_login_ip');
            $table->unsignedBigInteger('created_by')->nullable()->after('is_active');
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'role', 'is_main_admin', 'avatar',
                'last_login_at', 'last_login_ip', 'is_active', 'created_by',
            ]);
        });
    }
};
