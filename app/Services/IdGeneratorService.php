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
        $date = Carbon::now()->format('Ymd');
        $prefix = 'REG-' . $date . '-';
        
        // Find the last ID for today to get the highest sequence number
        $lastProfile = Profile::where('registration_id', 'like', $prefix . '%')
            ->orderBy('registration_id', 'desc')
            ->lockForUpdate()
            ->first();

        if ($lastProfile) {
            // Extract the number from 'REG-YYYYMMDD-XXX'
            $lastNumber = (int) substr($lastProfile->registration_id, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
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
        $date = Carbon::now()->format('Ymd');
        $prefix = 'ABS-' . $date . '-';

        // Find the last submission for today
        $lastPaper = Paper::where('submission_id', 'like', $prefix . '%')
            ->orderBy('submission_id', 'desc')
            ->lockForUpdate()
            ->first();

        if ($lastPaper) {
            $lastNumber = (int) substr($lastPaper->submission_id, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
