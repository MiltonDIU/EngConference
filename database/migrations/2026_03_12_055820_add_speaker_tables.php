<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('speakers', function (Blueprint $table) {

            $table->string('slug')->nullable();
            $table->boolean('show_home')->default(true);
            $table->integer('serial')->default(0);
            $table->foreignId('speaker_type_id')
                ->nullable()
                ->constrained('speaker_types');
        });
    }

    public function down(): void
    {
        Schema::table('speakers', function (Blueprint $table) {

            $table->dropForeign(['speaker_type_id']);
            $table->dropColumn([
                'show_home',
                'serial',
                'speaker_type_id'
            ]);

        });
    }
};
