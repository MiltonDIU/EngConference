<?php

namespace App\Services;

use App\Models\Profile;
use App\Models\Paper;
use Illuminate\Support\Carbon;

class IdGeneratorService
{
    /**
     * Generate a unique Registration ID for a profile.
     * Format: REG-YYYYMMDD-XXX
     * 
     * @return string
     */
    public static function generateRegistrationId()
    {
        $now = Carbon::now();
        $date = $now->format('y') . $now->month . $now->day;
        $prefix = 'REG-' . $date . '-';
        
        // Find the absolute last profile to get the highest sequence number regardless of date
        $lastProfile = Profile::orderBy('id', 'desc')
            ->lockForUpdate()
            ->first();

        $nextNumber = 1;
        if ($lastProfile && preg_match('/-(\d+)$/', $lastProfile->registration_id, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        } elseif ($lastProfile) {
            $nextNumber = Profile::count() + 1;
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a unique Submission ID for a paper.
     * Format: ABS-YYYYMMDD-XXX
     * 
     * @return string
     */
    public static function generateSubmissionId()
    {
        $now = Carbon::now();
        $date = $now->format('y') . $now->month . $now->day;
        $prefix = 'ABS-' . $date . '-';

        // Find the absolute last submission to get the highest sequence number regardless of date
        $lastPaper = Paper::orderBy('id', 'desc')
            ->lockForUpdate()
            ->first();

        $nextNumber = 1;
        if ($lastPaper && preg_match('/-(\d+)$/', $lastPaper->submission_id, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        } elseif ($lastPaper) {
            $nextNumber = Paper::count() + 1;
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
