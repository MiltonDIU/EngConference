<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Presentation upload gets its own deadline; paper_file_submission_deadline stays for the full paper.
        // Empty = same deadline as the full paper.
        if (!DB::table('settings')->where('key', 'presentation_submission_deadline')->exists()) {
            DB::table('settings')->insert([
                'key' => 'presentation_submission_deadline',
                'value' => '2026-11-15 23:59:59',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where('key', 'presentation_submission_deadline')->delete();
    }
};
