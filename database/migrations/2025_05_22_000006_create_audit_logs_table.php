<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Who performed the action (null = API/system)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_type')->default('user')
                ->comment('user|api_token|system');
            $table->string('actor_label')->nullable()
                ->comment('Token name or system identifier');

            // What was changed
            $table->string('event')->comment('license.created|license.revoked|batch.generated …');
            $table->string('auditable_type')->nullable()->comment('App\\Models\\License');
            $table->unsignedBigInteger('auditable_id')->nullable();

            // Context
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('metadata')->nullable()
                ->comment('ip, user_agent, request_id, …');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();

            // Replay-attack prevention
            $table->string('request_id', 64)->nullable()->unique()
                ->comment('Idempotency key from API caller');

            $table->timestamp('created_at')->useCurrent();

            // Indexes (no updated_at — audit rows are immutable)
            $table->index('event');
            $table->index('user_id');
            $table->index(['auditable_type', 'auditable_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
