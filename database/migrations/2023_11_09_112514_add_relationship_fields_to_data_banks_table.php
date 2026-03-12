<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToDataBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('data_banks', function (Blueprint $table) {
            $table->unsignedBigInteger('data_bank_category_id')->nullable();
            $table->foreign('data_bank_category_id', 'data_bank_category_fk_9198687')->references('id')->on('data_bank_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('data_banks', function (Blueprint $table) {
            //
        });
    }
}
