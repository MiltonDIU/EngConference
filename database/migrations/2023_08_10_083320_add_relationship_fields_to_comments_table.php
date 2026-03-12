<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->unsignedBigInteger('post_id')->nullable();
            $table->foreign('post_id', 'post_fk_8853261')->references('id')->on('posts');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id', 'user_fk_8853262')->references('id')->on('users');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id', 'parent_fk_8853267')->references('id')->on('comments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            //
        });
    }
}
