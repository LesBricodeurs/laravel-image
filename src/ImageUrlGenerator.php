<?php

namespace LesBricodeurs\LaravelImage;

use Storage;
use InvalidArgumentException;
use Illuminate\Contracts\Foundation\Application;

class ImageUrlGenerator
{
    /**
     * The instance of the Application
     *
     * @var Application
     */
    private $app;

    /**
     * ImageUrlGenerator constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Generate the url to an image
     *
     * @param $path
     * @param string|array|\ArrayAccess $modifiers
     * @return string
     */
    public function url($path, $modifiers = 'default')
    {
        if (is_string($modifiers)) {
            $modifiers = config('images.shortcuts.' . $modifiers) ?: [];
        }

        if (!is_array($modifiers)) {
            throw new InvalidArgumentException('Modifiers are either a string (see config.shortcut) or an array');
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