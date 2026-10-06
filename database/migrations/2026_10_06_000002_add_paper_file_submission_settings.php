<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $settings = [
        // Upload section stays hidden until management opens it from Settings
        'is_paper_file_submission_open' => 'false',
        // Empty = no deadline
        'paper_file_submission_deadline' => '',
        'full_paper_max_upload_size_mb' => '10',
        'presentation_max_upload_size_mb' => '20',
        // false = each file can be uploaded only once
        'allow_paper_file_reupload' => 'false',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->settings as $key => $value) {
            if (!DB::table('settings')->where('key', $key)->exists()) {
                DB::table('settings')->insert([
                    'key' => $key,
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->whereIn('key', array_keys($this->settings))->delete();
    }
};
