<?php

namespace Milton\Vaultix\Models;

use Illuminate\Database\Eloquent\Model;

class BackupJob extends Model
{
    protected $table = 'vaultix_jobs';
    protected $fillable = [
        'destination_id',
        'name',
        'type',
        'custom_folder_name',
        'notification_email',
        'notify_on_success',
        'notify_on_failure',
        'frequency',
        'last_run_at',
        'next_run_at',
        'is_enabled',
    ];

    public function destination() {
        return $this->belongsTo(BackupDestination::class, 'destination_id');
    }
}
