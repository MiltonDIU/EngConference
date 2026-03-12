<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title',100);
            $table->string('slug',120);
            $table->string('sub_title',200)->nullable();
            $table->string('youtube_link',200)->nullable();
            $table->string('contact_phone',100);
            $table->string('contact_email',100);
            $table->text('contact_address',200);
            $table->string('about_where',200);
            $table->string('about_when',200);
            $table->text('about_summary')->nullable();
            $table->longText('description')->nullable();
            $table->timestamp('date_time')->nullable();
            $table->timestamp('reg_close_time')->nullable();
            $table->enum('is_active',[0,1,2])->default(1);
            $table->enum('is_free',[0,1])->default(0);
            $table->enum('is_events',[0,1])->default(1);


            $table->string('speaker_title',50)->nullable();
            $table->string('speaker_sub_title',200)->nullable();
            $table->string('schedule_title',50)->nullable();
            $table->string('schedule_sub_title',200)->nullable();
            $table->string('venue_title',50)->nullable();
            $table->string('venue_sub_title',200)->nullable();
            $table->string('hotel_title',50)->nullable();
            $table->string('hotel_sub_title',200)->nullable();
            $table->string('gallery_title',50)->nullable();
            $table->string('gallery_sub_title',200)->nullable();
            $table->string('sponsors_title',50)->nullable();
            $table->string('sponsors_sub_title',200)->nullable();
            $table->string('faq_title',50)->nullable();
            $table->string('faq_sub_title',200)->nullable();
            $table->string('newsletter_title',50)->nullable();
            $table->string('newsletter_sub_title',200)->nullable();
            $table->string('ticket_title',50)->nullable();
            $table->string('ticket_sub_title',200)->nullable();
            $table->string('contact_title',50)->nullable();
            $table->string('contact_sub_title',200)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
