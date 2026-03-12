<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionRolePivotTable extends Migration
{
    public function up()
    {
        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('role_id', 'role_id_fk_383833')->references('id')->on('roles')->onDelete('cascade');
            $table->foreignId('permission_id', 'permission_id_fk_383833')->references('id')->on('permissions')->onDelete('cascade');
        });
    }
}
