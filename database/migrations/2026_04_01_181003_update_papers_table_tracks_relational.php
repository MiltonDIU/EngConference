<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePapersTableTracksRelational extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('papers', function (Blueprint $table) {
            // Drop old string-based columns
            $table->dropColumn(['track', 'sub_track']);
            
            // Add new relational columns
            $table->foreignId('track_id')->nullable()->after('submission_id')->constrained()->onDelete('set null');
            $table->foreignId('sub_track_id')->nullable()->after('track_id')->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('papers', function (Blueprint $table) {
            $table->dropForeign(['track_id']);
            $table->dropForeign(['sub_track_id']);
            $table->dropColumn(['track_id', 'sub_track_id']);
            
            $table->string('track')->nullable()->after('submission_id');
            $table->string('sub_track')->nullable()->after('track');
        });
    }
}
