<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationFieldScheduleSpeakerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_speaker', function (Blueprint $table) {

            $table->unsignedBigInteger('schedule_id');
            $table->foreign('schedule_id', 'parent_fk_889634163')->references('id')->on('schedules');
            $table->unsignedBigInteger('speaker_id');
            $table->foreign('speaker_id', 'parent_fk_88962134163')->references('id')->on('speakers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
