<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();

            // Who paid
            $table->foreignUuid('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->string('guest_email')->nullable();

            // Gateway
            $table->string('gateway');                                   // stripe | paypal | flutterwave | manual | paystack | etc.
            $table->string('gateway_transaction_id')->nullable()->index(); // gateway's own transaction ID
            $table->string('gateway_reference')->nullable();              // gateway's order/charge reference
            $table->json('gateway_response')->nullable();                 // raw response for debugging

            // Amounts
            $table->decimal('amount', 10, 2);
            $table->decimal('fee', 10, 2)->default(0);     // gateway processing fee if known
            $table->decimal('net', 10, 2)->default(0);     // amount - fee
            $table->string('currency', 3)->default('USD');

            // Status
            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'failed',
                'refunded',
                'partially_refunded',
                'disputed',
                'cancelled',
            ])->default('pending');

            $table->decimal('refunded_amount', 10, 2)->default(0);
            $table->timestamp('refunded_at')->nullable();
            $table->text('refund_reason')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index(['gateway', 'gateway_transaction_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
