<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consulting_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('customer_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('inquiry_type')->default('consulting'); // consulting, gig, contract, partnership
            $table->string('subject');
            $table->text('description');
            $table->string('budget_range')->nullable(); // e.g. "GHS 5,000 - 10,000"
            $table->string('timeline')->nullable(); // e.g. "1-3 months"
            $table->json('services_interested')->nullable();
            $table->string('how_heard')->nullable(); // google, referral, social, event
            $table->string('status')->default('new'); // new, reviewing, quoted, accepted, declined, completed
            $table->text('admin_notes')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consulting_inquiries');
    }
};
