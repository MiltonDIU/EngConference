<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttendanceStatusToAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->boolean('attendance_status')->default(0); // Assuming attendance_status is a boolean (0 for absent, 1 for present)
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('attendance_status');
        });
    }
}
