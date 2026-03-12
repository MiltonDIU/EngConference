<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToSchedulesTable extends Migration
{
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->foreignId('speaker_id', 'speaker_fk_383954')->references('id')->on('speakers');
        });
    }
}
