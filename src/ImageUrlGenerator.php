<?php

namespace LesBricodeurs\LaravelImage;

use Illuminate\Contracts\Foundation\Application;
use InvalidArgumentException;
use League\Glide\ServerFactory;
use Storage;

class ImageUrlGenerator
{

    /**
     * The instance of the Application
     *
     * @var Application
     */
    private $app;
    private $server;

    /**
     * ImageUrlGenerator constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->server = ServerFactory::create([
            'response'          => new LaravelResponseFactory(),
            'source'            => Storage::disk(config('images.source'))->getDriver(),
            'cache'             => Storage::disk(config('images.cache'))->getDriver(),
            'cache_path_prefix' => config('images.cache_path_prefix'),
            'base_url'          => config('images.base_url'),
        ]);
        $this->app = $app;
    }

    /**
     * Generate the url to an image
     *
     * @param                           $path
     * @param string|array|\ArrayAccess $modifiers
     * @return string
     */
    public function url($path, $modifiers = 'default')
    {
        if (pathinfo($path, PATHINFO_EXTENSION) == 'svg') {
            return Storage::disk(config('images.source'))->url($path);
        }
        
        if (pathinfo($path, PATHINFO_EXTENSION) == 'gif') {
            return Storage::disk(config('images.source'))->url($path);
        }

        if (is_string($modifiers)) {
            $modifiers = config('images.shortcuts.' . $modifiers) ?: [];
        }

        if ( ! is_array($modifiers)) {
            throw new InvalidArgumentException('Modifiers are either a string (see config.shortcut) or an array');
        }

        if (empty($modifiers)) {
            return Storage::disk(config('images.source'))->url($path);
        }

        try {
            if ($this->server->cacheFileExists($path, $modifiers) === true) {
                $path_cache = $this->server->getCachePath($path, $modifiers);
                $disk = Storage::disk(config('images.cache'));

                if (filter_var($disk->url($path_cache), FILTER_VALIDATE_URL)) {
                    return $disk->url($path_cache);
                }
           }
        } catch (\Exception $e) {
        }


        $config = '';

        foreach ($modifiers as $modifier => $value) {
            $config .= $modifier . '-' . $value . '/';
        }

        $config = trim($config, '/');

        return route(config('images.route.name'), ['path' => $path, 'config' => $config]);
    }

    /**
     * Flush the cache
     *
     * @param string $path
     * @return bool
     */
    public function flushCache($path = '')
    {
        return Storage::disk(config('images.cache'))->deleteDirectory(config('images.cache_path_prefix') . '/' . $path);
    }
}
