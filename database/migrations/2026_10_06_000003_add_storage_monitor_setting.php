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
        // Dashboard shows a red storage warning when free space drops below this (GB)
        // or below the estimated space still needed for paper uploads, whichever is higher
        if (!DB::table('settings')->where('key', 'storage_min_free_gb')->exists()) {
            DB::table('settings')->insert([
                'key' => 'storage_min_free_gb',
                'value' => '5',
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
        DB::table('settings')->where('key', 'storage_min_free_gb')->delete();
    }
};
