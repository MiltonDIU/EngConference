<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('vaultix_destinations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('provider', ['gdrive', 's3', 'r2', 'sftp']);
            $table->json('credentials');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('vaultix_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained('vaultix_destinations')->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['db_only', 'files_only', 'full'])->default('full');
            $table->string('custom_folder_name')->nullable();
            $table->string('notification_email')->nullable();
            $table->boolean('notify_on_success')->default(true);
            $table->boolean('notify_on_failure')->default(true);
            $table->enum('frequency', ['daily', 'weekly', 'monthly'])->default('daily');
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vaultix_jobs');
        Schema::dropIfExists('vaultix_destinations');
    }
};
