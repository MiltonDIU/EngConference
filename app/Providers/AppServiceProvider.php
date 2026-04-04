<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('renderOembed', function ($expression) {
            return "<?php echo App\\Providers\\AppServiceProvider::renderOembed($expression); ?>";
        });

        // Auto-trigger queue worker when a job is queued
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Queue\Events\JobQueued::class, function ($event) {
            if (config('queue.default') === 'database') {
                $command = 'php ' . base_path('artisan') . ' queue:work --stop-when-empty';
                if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
                    pclose(popen("start /B " . $command, "r"));
                } else {
                    exec($command . ' > /dev/null 2>&1 &');
                }
            }
        });
    }

// Add a static method to handle the rendering of oembed tags
    public static function renderOembed($content)
    {
        // Use a regular expression to find and replace oembed tags with the desired format
        return preg_replace_callback('/<oembed url="([^"]+)"><\/oembed>/', function ($matches) {
            $url = $matches[1];
            $embedUrl = preg_replace('/youtu\.be\/(.+)/', 'www.youtube.com/embed/$1', $url);
            return '<figure class="media"><iframe width="560" height="315" src="' . $embedUrl . '" frameborder="0" allowfullscreen></iframe></figure>';
        }, $content);
    }
}
