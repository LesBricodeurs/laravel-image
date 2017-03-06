<?php

namespace LesBricodeurs\LaravelImage;

use Illuminate\Support\Facades\Facade;

class ImageFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Image';
    }
}