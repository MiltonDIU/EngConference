<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataBankDataBankCategoryPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_bank_data_bank_category', function (Blueprint $table) {
            $table->unsignedBigInteger('data_bank_id');
            $table->foreign('data_bank_id', 'data_bank_id_fk_9199191')->references('id')->on('data_banks')->onDelete('cascade');
            $table->unsignedBigInteger('data_bank_category_id');
            $table->foreign('data_bank_category_id', 'data_bank_category_id_fk_9199191')->references('id')->on('data_bank_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_bank_data_bank_category_pivot');
    }
}
