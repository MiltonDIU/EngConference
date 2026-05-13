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
        try {
            \Illuminate\Support\Facades\Storage::extend('google', function ($app, $config) {
                $options = [];
                if (!empty($config['teamDriveId'] ?? null)) {
                    $options['teamDriveId'] = $config['teamDriveId'];
                }

                $client = new \Google\Client();
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);

                $service = new \Google\Service\Drive($client);
                $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, $config['folderId'] ?? '/', $options);
                $driver = new \League\Flysystem\Filesystem($adapter);

                return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
            });
        } catch (\Exception $e) {
            // Silently fail if dependencies are missing during initial load
        }
    }

    public function register()
    {
        //
    }
}
