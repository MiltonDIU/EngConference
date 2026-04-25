<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('committee_conference_member', function (Blueprint $table) {
            $table->id();
            $table->foreignId('committee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('conference_member_id')->constrained('conference_members')->cascadeOnDelete();
            $table->string('role')->nullable(); // e.g., Chair, Member
            $table->string('level')->nullable(); // e.g., Honorary, Operational
            $table->string('remarks')->nullable(); // e.g., Consent pending
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('committee_conference_member');
    }
};
