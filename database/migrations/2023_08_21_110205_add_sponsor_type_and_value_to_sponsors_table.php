<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSponsorTypeAndValueToSponsorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sponsors', function (Blueprint $table) {
            $table->unsignedBigInteger('sponsor_type_id')->nullable();
            $table->foreign('sponsor_type_id', 'sponsor_type_fk_8896363')->references('id')->on('sponsor_types');
            $table->float('amount', 15, 2)->default(0);
            $table->text('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sponsors', function (Blueprint $table) {
            //
        });
    }
}
