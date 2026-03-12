<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleUserPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_user', function (Blueprint $table) {

                $table->foreignId('user_id', 'user_id_fk_383842')->references('id')->on('users')->onDelete('cascade');
                $table->foreignId('schedule_id', 'schedule_id_fk_383842')->references('id')->on('schedules')->onDelete('cascade');
            });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_user');
    }
}
