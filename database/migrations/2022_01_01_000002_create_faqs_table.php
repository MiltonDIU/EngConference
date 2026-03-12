<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaqsTable extends Migration
{
    public function up()
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();

            $table->string('question',250);

            $table->string('answer',250);

            $table->timestamps();

            $table->softDeletes();
        });
    }
}
