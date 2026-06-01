<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Enterprise Licensing Platform — Phase 2
 *
 * Adds to every key entity table:
 *   • uuid column (stable internal anchor; survives key regeneration)
 *
 * Adds to `licenses`:
 *   • features (JSON)          — per-license entitlement flags
 *   • revocation_checksum      — lightweight offline revocation detection
 *   • min_app_version / max_app_version — product version gating
 *
 * Adds to `products`:
 *   • min_app_version / max_app_version — default version constraints
 *   • offline_ttl_hours                 — signed-response cache window
 *   • grace_period_days (product default)
 *
 * Adds to `validation_logs`:
 *   • response_nonce   — server-emitted nonce for bidirectional replay protection
 *   • validation_source — online|offline|cached|grace_period
 *   • offline_valid_until
 */
return new class extends Migration
{
    // ──────────────────────────────────────────────────────────────────────────
    // UP
    // ──────────────────────────────────────────────────────────────────────────

    public function up(): void
    {
        // ── 1. Add UUIDs to every key entity table ────────────────────────────

        $tables = [
            'customers',
            'licenses',
            'license_activations',
            'license_batches',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->uuid('uuid')->nullable()->unique()->after('id')
                    ->comment('Stable internal UUID; survives key regeneration');
            });
        }

        // Back-fill UUIDs for any existing rows
        foreach ($tables as $table) {
            DB::table($table)->whereNull('uuid')->orderBy('id')->each(function ($row) use ($table) {
                DB::table($table)->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]);
            });
        }

        // ── 2. License: features, revocation_checksum, version constraints ────

        Schema::table('licenses', function (Blueprint $table) {
            $table->json('features')->nullable()->after('metadata')
                ->comment('Array of feature-flag strings this license is entitled to');
            $table->string('revocation_checksum', 64)->nullable()->after('features')
                ->comment('SHA-256 of (uuid + status + revoked_at); changes on any revocation event');
            $table->string('min_app_version', 32)->nullable()->after('revocation_checksum')
                ->comment('Minimum app version required for this license to be valid');
            $table->string('max_app_version', 32)->nullable()->after('min_app_version')
                ->comment('Maximum app version allowed for this license (null = no upper bound)');
        });

        // ── 3. Products: version constraints + offline TTL + grace default ────



        // ── 4. Validation logs: response nonce, source, offline_valid_until ──

        Schema::table('validation_logs', function (Blueprint $table) {
            $table->string('response_nonce', 64)->nullable()->after('request_nonce')
                ->comment('Server-emitted nonce returned in the validation response');
            $table->string('validation_source', 32)->default('online')->after('response_nonce')
                ->comment('online|offline|cached|grace_period');
            $table->timestamp('offline_valid_until')->nullable()->after('validation_source')
                ->comment('Timestamp until which the cached response remains valid offline');
        });
    }

    // ──────────────────────────────────────────────────────────────────────────
    // DOWN
    // ──────────────────────────────────────────────────────────────────────────

    public function down(): void
    {
        Schema::table('validation_logs', function (Blueprint $table) {
            $table->dropColumn(['response_nonce', 'validation_source', 'offline_valid_until']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['min_app_version', 'max_app_version', 'offline_ttl_hours', 'grace_period_days']);
        });

        Schema::table('licenses', function (Blueprint $table) {
            $table->dropColumn(['features', 'revocation_checksum', 'min_app_version', 'max_app_version']);
        });

        $tables = [
            'license_batches',
            'license_activations',
            'licenses',
            'customers',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn('uuid');
            });
        }
    }
};
