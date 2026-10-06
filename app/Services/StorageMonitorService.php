<?php

namespace App\Services;

use App\Models\Paper;
use App\Models\PaperFile;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class StorageMonitorService
{
    private const CACHE_KEY = 'storage_monitor_status';
    private const CACHE_SECONDS = 600;


    public static function status(bool $fresh = false): array
    {
        if ($fresh) {
            Cache::forget(self::CACHE_KEY);
        }

        return Cache::remember(self::CACHE_KEY, self::CACHE_SECONDS, function () {
            $space = self::cpanelQuota() ?? self::diskSpace();
            $estimate = self::remainingUploadEstimate();
            $minFreeGb = (float) (Setting::where('key', 'storage_min_free_gb')->value('value') ?: 5);

            $free = $space['free'];
            $required = max($minFreeGb * 1073741824, $estimate['bytes']);
            $isLow = $free !== null && $free < $required;

            return $space + [
                'paper_files_bytes' => (int) PaperFile::sum('size'),
                'paper_files_count' => PaperFile::count(),
                'estimate' => $estimate,
                'min_free_gb' => $minFreeGb,
                'is_low' => $isLow,
                'checked_at' => now(),
            ];
        });
    }

    public static function formatBytes(?float $bytes): string
    {
        if ($bytes === null) {
            return 'N/A';
        }
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }

        return number_format($bytes / 1048576, 1) . ' MB';
    }

    /**
     * Account quota on cPanel hosting. disk_free_space() there reports the whole
     * server partition, not the account's plan limit, so ask cPanel first.
     */
    private static function cpanelQuota(): ?array
    {
        try {
            $result = Process::timeout(10)->run(['uapi', '--output=json', 'Quota', 'get_quota_info']);
            if (!$result->successful()) {
                return null;
            }

            $data = json_decode($result->output(), true)['result']['data'] ?? null;
            if (!is_array($data)) {
                return null;
            }

            $limit = isset($data['byte_limit']) ? (float) $data['byte_limit'] : (float) ($data['megabyte_limit'] ?? 0) * 1048576;
            $used = isset($data['bytes_used']) ? (float) $data['bytes_used'] : (float) ($data['megabytes_used'] ?? 0) * 1048576;

            // 0 = unlimited quota; then the physical disk is the real limit
            if ($limit <= 0) {
                return null;
            }

            return [
                'source' => 'cPanel account quota',
                'total' => $limit,
                'used' => $used,
                'free' => max(0, $limit - $used),
            ];
        } catch (\Throwable $e) {
            // uapi missing or proc_open disabled: fall back to the disk check
            return null;
        }
    }

    private static function diskSpace(): array
    {
        $path = storage_path('app');
        $free = @disk_free_space($path);
        $total = @disk_total_space($path);

        if ($free === false || $total === false) {
            Log::warning('Storage monitor: unable to read disk space for ' . $path);
            return ['source' => 'Unavailable', 'total' => null, 'used' => null, 'free' => null];
        }

        return [
            'source' => 'Server disk',
            'total' => (float) $total,
            'used' => (float) $total - (float) $free,
            'free' => (float) $free,
        ];
    }

    /**
     * Worst-case space still needed: every approved paper uploads each missing file
     * at the maximum allowed size (full_paper / presentation_max_upload_size_mb).
     */
    private static function remainingUploadEstimate(): array
    {
        $settings = PaperFileService::settings();
        $approvedPapers = Paper::where('status', 'approved')->count();
        $bytes = 0;
        $missing = [];
        $maxSizeMb = [];

        foreach (array_keys(PaperFileService::TYPES) as $type) {
            $submitted = Paper::where('status', 'approved')
                ->whereHas('files', fn ($q) => $q->where('type', $type))
                ->count();

            $missing[$type] = max(0, $approvedPapers - $submitted);
            $maxSizeMb[$type] = PaperFileService::maxSizeMb($type, $settings);
            $bytes += $missing[$type] * $maxSizeMb[$type] * 1048576;
        }

        return [
            'approved_papers' => $approvedPapers,
            'missing' => $missing,
            'max_size_mb' => $maxSizeMb,
            'bytes' => $bytes,
        ];
    }
}
