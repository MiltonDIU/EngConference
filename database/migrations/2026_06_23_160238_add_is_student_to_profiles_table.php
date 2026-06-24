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
        Schema::table('profiles', function (Blueprint $table) {
            $table->boolean('author_list_confirmed')->default(false)->after('payment_status');
        });

        Schema::table('paper_authors', function (Blueprint $table) {
            $table->boolean('is_student')->nullable()->after('is_presenting_author');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('author_list_confirmed');
        });

        Schema::table('php ', function (Blueprint $table) {
            $table->dropColumn('is_student');
        });
    }
};
