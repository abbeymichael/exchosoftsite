<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('license_key')->unique();
            $table->enum('edition', ['standard', 'professional', 'enterprise', 'trial'])->default('standard');
            $table->enum('type', ['lifetime', 'monthly', 'annual', 'trial', 'floating', 'multi-device'])->default('lifetime');
            $table->integer('max_activations')->default(1);
            $table->integer('current_activations')->default(0);
            $table->enum('status', ['active', 'expired', 'suspended', 'revoked', 'trial'])->default('active');
            $table->timestamp('expires_at')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
