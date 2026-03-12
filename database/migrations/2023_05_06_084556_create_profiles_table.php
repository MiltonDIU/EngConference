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
            $table->string('phone');
            $table->string('gender')->nullable();
            $table->string('institute_name');

            $table->string('academic_major')->nullable();
            $table->string('part_aws_cloud_club')->nullable();
            $table->string('tracks_like')->nullable();
            $table->string('aws_familiar')->nullable();
            $table->text('comments')->nullable();
            $table->text('production_app')->nullable();
            $table->text('application_url')->nullable();
            $table->text('logo_url')->nullable();


            $table->float('pay_amount')->default(0);
            $table->integer('identity_no')->default(0);
            $table->string('coupon_code')->nullable();
            $table->enum('payment_status',['0','1'])->default('0');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
