<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmenityPricePivotTable extends Migration
{
    public function up()
    {
        Schema::create('amenity_price', function (Blueprint $table) {

            $table->foreignId('price_id', 'price_id_fk_384063')->references('id')->on('prices')->onDelete('cascade');
            $table->foreignId('amenity_id', 'amenity_id_fk_384063')->references('id')->on('amenities')->onDelete('cascade');
        });
    }
}
