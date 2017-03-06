<?php
namespace LesBricodeurs\LaravelImage;

use Illuminate\Support\ServiceProvider;

class ImageServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function register()
    {
        $this->publishes([
            __DIR__ . '/../config/images.php' => config_path('images.php')
        ]);

        $this->app->singleton('Image', function($app) {
            return new ImageUrlGenerator($app);
        });
    }

    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/images.php', 'images');

        $this->app['router']->get(config('images.route.url') . '/{path}/{config?}', config('images.route.action'))
            ->name(config('images.route.name'))
            ->where('path', config('images.route.path_pattern'))
            ->where('config', '.*');
    }

    public function provides()
    {
        return ['Image'];
    }
}