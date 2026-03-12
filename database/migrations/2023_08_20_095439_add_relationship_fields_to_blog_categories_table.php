<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToBlogCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blog_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id', 'parent_fk_8896363')->references('id')->on('blog_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blog_categories', function (Blueprint $table) {
            //
        });
    }
}
