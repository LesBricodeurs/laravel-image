<?php

namespace LesBricodeurs\LaravelImage;

use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use League\Glide\ServerFactory;
use Storage;

class ImageController extends Controller
{

    private $server;

    public function __construct(Request $request)
    {
        session_write_close();
        $this->server = ServerFactory::create([
            'response'          => new LaravelResponseFactory($request),
            'source'            => Storage::disk(config('images.source'))->getDriver(),
            'cache'             => Storage::disk(config('images.cache'))->getDriver(),
            'cache_path_prefix' => config('images.cache_path_prefix'),
            'base_url'          => config('images.base_url'),
        ]);
    }

    public function show($path, $config = '')
    {
        try {
            if ($this->server->cacheFileExists($path, $this->parseConfig($config)) === true) {
                $path = $this->server->getCachePath($path, $this->parseConfig($config));
            }
            else {
                $path = $this->server->makeImage($path, $this->parseConfig($config));
                $realpath = Storage::disk(config('images.cache'))->path($path);
                ImageOptimizer::optimize($realpath);
            }
            return $this->server->getResponseFactory()->create($this->server->getCache(), $path);
        } catch (\Exception $e) {
            return abort(404);
        }
    }

    private function parseConfig($config)
    {
        $modifiers = [];

        foreach (explode('/', $config) as $modifier) {
            $modifier = explode('-', $modifier);

            if (count($modifier) == 2) {
                $modifiers[$modifier[0]] = $modifier[1];
            }
        }

        return $modifiers;
    }
}