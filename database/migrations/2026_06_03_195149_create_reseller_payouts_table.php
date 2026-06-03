<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reseller_payouts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('reseller_id')->constrained()->cascadeOnDelete();

            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');

            $table->string('method');                             // bank | paypal | crypto | manual
            $table->string('reference')->nullable();              // your bank ref, PayPal tx ID, etc.

            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');

            // Period this payout covers
            $table->date('period_from')->nullable();
            $table->date('period_to')->nullable();

            // Admin who triggered the payout
            $table->foreignUuid('processed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['reseller_id', 'status']);
        });

        // Now that reseller_payouts exists, we can add the FK on reseller_commissions
        // (defined here because commissions references payouts but was created first)
        // — handled in the commissions migration via deferred FK; nothing to do here.
    }

    public function down(): void
    {
        Schema::dropIfExists('reseller_payouts');
    }
};
