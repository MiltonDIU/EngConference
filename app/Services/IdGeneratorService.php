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
        $count = Profile::where('registration_id', 'like', 'REG-' . $date . '-%')->count() + 1;
        return 'REG-' . $date . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
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
        $count = Paper::where('submission_id', 'like', 'ABS-' . $date . '-%')->count() + 1;
        return 'ABS-' . $date . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}
