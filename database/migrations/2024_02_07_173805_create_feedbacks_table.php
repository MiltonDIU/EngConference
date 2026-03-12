<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('schedule_id')->nullable();
            $table->foreign('schedule_id')->references('id')->on('schedules');
            $table->string('comment')->nullable();
            $table->enum('rating',[1,2,3,4,5])->default(5);
            $table->timestamps();

            // Add unique constraint across user_id and schedule_id
            $table->unique(['user_id', 'schedule_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feedbacks');
    }
}
