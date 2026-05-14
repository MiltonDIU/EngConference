<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // 1. Destinations Table
        Schema::create('vaultix_destinations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('provider', ['gdrive', 's3', 'r2', 'sftp']);
            $table->json('credentials');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Backup Jobs Table
        Schema::create('vaultix_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained('vaultix_destinations')->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['db_only', 'files_only', 'full'])->default('full');
            $table->string('custom_folder_name')->nullable();
            $table->string('notification_email')->nullable();
            $table->boolean('notify_on_success')->default(true);
            $table->boolean('notify_on_failure')->default(true);
            $table->enum('frequency', ['hourly', 'daily', 'weekly', 'monthly', '6_hours', '12_hours'])->default('daily');
            
            // Retention Settings
            $table->integer('keep_all_backups_for_days')->default(7);
            $table->integer('keep_daily_backups_for_days')->default(16);
            $table->integer('keep_weekly_backups_for_weeks')->default(8);
            $table->integer('keep_monthly_backups_for_months')->default(4);
            
            // Schedule Settings
            $table->string('backup_time')->default('00:00');
            $table->string('backup_day')->nullable();

            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        // 3. Backup History Table
        Schema::create('vaultix_backups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('vaultix_jobs')->onDelete('cascade');
            $table->foreignId('destination_id')->constrained('vaultix_destinations')->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_name');
            $table->bigInteger('file_size')->default(0);
            $table->string('disk')->nullable();
            $table->string('status')->default('success'); // Added Missing Status Column
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // 4. Settings Table
        Schema::create('vaultix_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed Default Settings
        DB::table('vaultix_settings')->insert([
            [
                'key' => 'authorized_emails',
                'value' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'storage_threshold_mb',
                'value' => '500',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('vaultix_settings');
        Schema::dropIfExists('vaultix_backups');
        Schema::dropIfExists('vaultix_jobs');
        Schema::dropIfExists('vaultix_destinations');
    }
};
