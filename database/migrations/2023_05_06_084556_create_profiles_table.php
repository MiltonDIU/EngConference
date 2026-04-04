<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('designation');
            $table->string('department')->nullable();
            $table->string('institution');
            $table->unsignedBigInteger('country_id');
            $table->string('registration_id')->unique()->nullable();
            $table->string('whatsapp_number');
            $table->boolean('is_author')->default(false);
            $table->enum('participation_mode', ['onsite', 'online']);

            $table->float('pay_amount')->default(0);
            $table->string('currency')->default('BDT');
            $table->enum('payment_status', ['0', '1'])->default('0');

            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('country_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}
