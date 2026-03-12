<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationCouponReferralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->unsignedBigInteger('coupon_id')->nullable()->unique();
            $table->foreign('coupon_id', 'parent_fk_88963416320')->references('id')->on('coupons');
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
