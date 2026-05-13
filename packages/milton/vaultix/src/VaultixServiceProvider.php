<?php

namespace Milton\Vaultix;

use Illuminate\Support\ServiceProvider;
use Milton\Vaultix\Commands\VaultixBackupCommand;

class VaultixServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'vaultix');

        $this->registerGoogleDriver();

        $this->commands([
            VaultixBackupCommand::class,
        ]);
    }

    protected function registerGoogleDriver()
    {
        // We only need to extend the storage with 'google' driver.
        // For S3 and others, Laravel's built-in drivers work perfectly 
        // as long as we define the 'vaultix_disk' configuration at runtime.
        try {
            if (class_exists(\Masbug\Flysystem\GoogleDriveAdapter::class)) {
                \Illuminate\Support\Facades\Storage::extend('google', function ($app, $config) {
                    $options = [];
                    if (!empty($config['teamDriveId'] ?? null)) {
                        $options['teamDriveId'] = $config['teamDriveId'];
                    }

                    $client = new \Google\Client();
                    $client->setClientId($config['clientId'] ?? '');
                    $client->setClientSecret($config['clientSecret'] ?? '');
                    $client->refreshToken($config['refreshToken'] ?? '');

                    $service = new \Google\Service\Drive($client);
                    $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, $config['folderId'] ?? '/', $options);
                    $driver = new \League\Flysystem\Filesystem($adapter);

                    return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
                });
            }
        } catch (\Exception $e) {
            // Silently fail to avoid crashing the app if dependencies are being installed
        }
    }

    public function register()
    {
        //
    }
}
