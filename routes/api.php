<?php

use App\Http\Controllers\Api\V1\Internal\ProvisioningController;
use App\Http\Controllers\Api\V1\LicenseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ExchoLicense API v1
|--------------------------------------------------------------------------
| Public:   license validation, activation, deactivation, renewal, status
| Internal: provisioning (Sanctum-protected, rate-limited)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->name('api.v1.')->group(function () {

    // ── Public License Endpoints ──────────────────────────────────────────────
    // Rate-limited to 60 rpm to deter brute-force / replay attacks
    Route::middleware(['throttle:60,1'])->prefix('licenses')->name('licenses.')->group(function () {

        // POST /api/v1/licenses/validate  — validate + activate a device
        Route::post('/validate', [LicenseController::class, 'validate'])->name('validate');

        // POST /api/v1/licenses/renew     — request a license renewal
        Route::post('/renew', [LicenseController::class, 'renew'])->name('renew');

        // GET  /api/v1/licenses/status    — check status without activating
        Route::get('/status', [LicenseController::class, 'status'])->name('status');

        // POST /api/v1/licenses/deactivate — deactivate a device
        Route::post('/deactivate', [LicenseController::class, 'deactivate'])->name('deactivate');

    });

    // ── Internal Provisioning API (Sanctum-protected) ─────────────────────────
    // Used by your e-commerce website / external systems to provision licenses
    // Rate-limited to 120 rpm per token
    Route::middleware(['auth:sanctum', 'throttle:120,1'])
        ->prefix('internal')
        ->name('internal.')
        ->group(function () {

            // Create a single license (optionally customer-linked)
            Route::post('/licenses/create', [ProvisioningController::class, 'create'])
                ->name('licenses.create');

            // Bulk-generate licenses into a named batch
            Route::post('/licenses/bulk-create', [ProvisioningController::class, 'bulkCreate'])
                ->name('licenses.bulk_create');

            // Create a trial license
            Route::post('/licenses/create-trial', [ProvisioningController::class, 'createTrial'])
                ->name('licenses.create_trial');

            // Extend license expiry
            Route::post('/licenses/extend', [ProvisioningController::class, 'extend'])
                ->name('licenses.extend');

            // Revoke a license permanently
            Route::post('/licenses/revoke', [ProvisioningController::class, 'revoke'])
                ->name('licenses.revoke');

            // Suspend a license temporarily
            Route::post('/licenses/suspend', [ProvisioningController::class, 'suspend'])
                ->name('licenses.suspend');

            // Unsuspend / re-activate
            Route::post('/licenses/unsuspend', [ProvisioningController::class, 'unsuspend'])
                ->name('licenses.unsuspend');

            // Reset all device activations
            Route::post('/licenses/reset-devices', [ProvisioningController::class, 'resetDevices'])
                ->name('licenses.reset_devices');

            // Regenerate the license key (old key instantly invalidated)
            Route::post('/licenses/regenerate-key', [ProvisioningController::class, 'regenerateKey'])
                ->name('licenses.regenerate_key');

            // Append or replace notes on a license
            Route::post('/licenses/attach-notes', [ProvisioningController::class, 'attachNotes'])
                ->name('licenses.attach_notes');

            // Look up a single license by key
            Route::get('/licenses/{key}', [ProvisioningController::class, 'show'])
                ->name('licenses.show');

        });

});
