<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReviewFieldsToPapersTable extends Migration
{
    public function up()
    {
        Schema::table('papers', function (Blueprint $table) {
            $table->text('review_note')->nullable()->after('status');
            $table->unsignedBigInteger('reviewed_by')->nullable()->after('review_note');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');

            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('papers', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['review_note', 'reviewed_by', 'reviewed_at']);
        });
    }
}
