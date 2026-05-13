# Vaultix

Vaultix is a dynamic, admin-managed backup system for Laravel. It provides a robust interface and command-line tools to back up your application files and database to multiple cloud storage providers such as Google Drive, Amazon S3, and Cloudflare R2.

## Dependencies

This package relies on the following major dependencies:
- **php**: `^8.2`
- **laravel/framework**: `^10.0 | ^11.0`
- **spatie/laravel-backup**: `^9.0` (Handles the core backup archiving and database dumping)
- **masbug/flysystem-google-drive-ext**: `^1.0` (Google Drive Flysystem adapter for V3)

> **Note:** You **do not** need to install these dependencies manually. When you install Vaultix via Composer, it will automatically pull in these required packages into your project if they are not already installed.

## Installation

1. Require the package via Composer:
```bash
composer require milton/vaultix
```

*(If you are developing locally and the package is in a local directory, ensure your project's `composer.json` is configured to load the local repository).*

2. Publish the configuration, migrations, and assets:
```bash
php artisan vendor:publish --provider="Milton\Vaultix\VaultixServiceProvider"
```

3. Run the migrations to create the necessary database tables (`vaultix_settings`, `vaultix_backups`):
```bash
php artisan migrate
```

## Configuration

Vaultix provides a dynamic configuration interface. Once installed, navigate to the Vaultix admin dashboard in your application to configure your backup destinations.

### Google Drive Setup

To use Google Drive, you will need a Google Service Account:
1. Go to the [Google Cloud Console](https://console.cloud.google.com/).
2. Create a new Service Account and download the JSON credentials file.
3. Share your target Google Drive folder with the Service Account email address.
4. Input the Folder ID and upload the JSON credentials through the Vaultix interface.

## Usage

Vaultix primarily runs via a background job queue to prevent timeouts during large backups.

### Triggering a Backup

You can trigger backups from the Vaultix UI, which dispatches a queued job (`ProcessVaultixBackup`). Make sure your Laravel queue worker is running:

```bash
php artisan queue:work
```

### Supported Backup Types

- **Database Only:** Dumps your database (MySQL, PostgreSQL, etc.) and uploads the SQL file.
- **Files Only:** Zips your application files (excluding `vendor` and `node_modules`) and uploads the archive.
- **Full Backup:** Archives both the database and the application files.

## Scheduling Backups

While you can trigger backups manually from the UI, you can also schedule them using Laravel's task scheduler.

Add the following to your `app/Console/Kernel.php` (or `routes/console.php` in Laravel 11):

```php
protected function schedule(Schedule $schedule)
{
    // Example: Run the default Spatie backup command daily
    $schedule->command('backup:run')->daily();
    $schedule->command('backup:clean')->daily();
}
```
*(Note: For specific Vaultix job execution via schedule, refer to your dynamic settings configuration).*

## Notifications

Vaultix uses Spatie's notification system. You can configure the notification email directly from the Vaultix settings dashboard. When a backup succeeds or fails, an email will be dispatched to the provided address.

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
