<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventSpeakerPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_speaker', function (Blueprint $table) {
            $table->foreignId('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreignId('speaker_id')->references('id')->on('speakers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_speaker');
    }
}
