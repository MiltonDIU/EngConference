<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventSponsorPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_sponsor', function (Blueprint $table) {
            $table->foreignId('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreignId('sponsor_id')->references('id')->on('sponsors')->onDelete('cascade');
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
        Schema::dropIfExists('event_sponsor');
    }
}
