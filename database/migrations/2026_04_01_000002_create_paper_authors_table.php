<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaperAuthorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paper_authors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paper_id');
            $table->string('name');
            $table->string('designation');
            $table->string('department')->nullable();
            $table->string('institution');
            $table->unsignedBigInteger('country_id');
            $table->string('email');
            $table->boolean('is_presenting_author')->default(false);
            $table->integer('author_order');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('paper_id')->references('id')->on('papers')->onDelete('cascade');
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
        Schema::dropIfExists('paper_authors');
    }
}
