<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralVisitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_visitors', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address')->nullable();
            $table->string('referral_identification');
            $table->string('user_agent')->nullable();
            $table->string('cookie_value');
            $table->string('cookie_name');
            $table->string('minutes');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id', 'user_fk_referral4')->references('id')->on('users');
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
        Schema::dropIfExists('referral_visitors');
    }
}
