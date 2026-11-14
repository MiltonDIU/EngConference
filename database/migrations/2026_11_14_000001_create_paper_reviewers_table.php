<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Which reviewer (session chair) may view which paper
        Schema::create('paper_reviewers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paper_id')->constrained('papers')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('session_no')->nullable();
            $table->string('session_info')->nullable(); // e.g. "4 Dec 2026 · Onsite · 11:30–13:00"
            $table->unsignedTinyInteger('position')->nullable(); // presentation order inside the session
            $table->timestamps();

            $table->unique(['paper_id', 'user_id']);
            $table->index('user_id');
        });

        // Label only: access comes from paper_reviewers, so the role carries no permissions
        if (!DB::table('roles')->where('title', 'Reviewer')->exists()) {
            DB::table('roles')->insert([
                'title' => 'Reviewer',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paper_reviewers');

        $roleId = DB::table('roles')->where('title', 'Reviewer')->value('id');
        if ($roleId) {
            DB::table('role_user')->where('role_id', $roleId)->delete();
            DB::table('roles')->where('id', $roleId)->delete();
        }
    }
};
