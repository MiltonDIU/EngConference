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
        // true = admins can upload / replace Full Paper & Presentation files on the author's behalf
        if (!DB::table('settings')->where('key', 'allow_admin_paper_file_upload')->exists()) {
            DB::table('settings')->insert([
                'key' => 'allow_admin_paper_file_upload',
                'value' => 'true',
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
        DB::table('settings')->where('key', 'allow_admin_paper_file_upload')->delete();
    }
};
