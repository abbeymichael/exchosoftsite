<?php

namespace App\Services;

use App\Models\BatchExport;
use App\Models\License;
use App\Models\LicenseBatch;
use Illuminate\Support\Facades\Storage;

class BatchExportService
{
    /**
     * Export all licenses in a batch to a CSV file and store it.
     * Returns a BatchExport record with a downloadable storage path.
     */
    public function exportCsv(LicenseBatch $batch, int $exportedBy): BatchExport
    {
        $filename    = "batch-{$batch->batch_code}-export-" . now()->format('Ymd-His') . '.csv';
        $storagePath = "exports/{$filename}";

        // Build CSV content
        $licenses = License::where('batch_id', $batch->id)
            ->with(['customer'])
            ->orderBy('id')
            ->get();

        $csvLines   = [];
        $csvLines[] = implode(',', [
            'License Key', 'Status', 'Edition', 'Type',
            'Max Activations', 'Current Activations',
            'Expires At', 'Customer Name', 'Customer Email',
            'Order ID', 'Created At',
        ]);

        foreach ($licenses as $license) {
            $csvLines[] = implode(',', array_map(
                fn ($v) => '"' . str_replace('"', '""', (string) $v) . '"',
                [
                    $license->license_key,
                    $license->status,
                    $license->edition,
                    $license->type,
                    $license->max_activations,
                    $license->current_activations,
                    $license->expires_at?->toDateString() ?? 'lifetime',
                    $license->customer?->name ?? '',
                    $license->customer?->email ?? '',
                    $license->order_id ?? '',
                    $license->created_at->toDateTimeString(),
                ]
            ));
        }

        $csvContent = implode("\n", $csvLines);

        Storage::put($storagePath, $csvContent);

        $export = BatchExport::create([
            'batch_id'     => $batch->id,
            'exported_by'  => $exportedBy,
            'filename'     => $filename,
            'format'       => 'csv',
            'record_count' => $licenses->count(),
            'storage_path' => $storagePath,
            'expires_at'   => now()->addDays(7), // auto-expire download
        ]);

        AuditService::log('batch.exported', $batch, [
            'filename'     => $filename,
            'record_count' => $licenses->count(),
        ]);

        return $export;
    }

    /**
     * Get the raw CSV content for streaming download.
     */
    public function getCsvContent(BatchExport $export): ?string
    {
        if (! $export->storage_path || ! Storage::exists($export->storage_path)) {
            return null;
        }

        return Storage::get($export->storage_path);
    }
}
