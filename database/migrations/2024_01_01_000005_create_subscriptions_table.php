<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained()->cascadeOnDelete();
            $table->enum('billing_cycle', ['monthly', 'annual'])->default('monthly');
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->timestamp('next_billing_date')->nullable();
            $table->string('provider')->nullable(); // stripe, paypal, etc.
            $table->string('provider_reference')->nullable();
            $table->enum('status', ['active', 'cancelled', 'past_due', 'trialing', 'paused'])->default('active');
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
