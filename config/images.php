<?php

return [
    /*
     * The disk where images a stored
     */
    'source' => 'public',

    /**
     * The disk where images cache is stored
     */
    'cache' => 'public',

    /**
     * Cache directory prefix
     */
    'cache_path_prefix' => '.cache',

    /**
     * The route to get images
     */
    'route' => [
        /**
         * The url of the route
         */
        'url' => 'images',

        /**
         * The name of the route
         */
        'name' => 'image.show',

        /**
         * The closure to call to get an image
         */
        'action' => 'LesBricodeurs\\LaravelImage\ImageController@show',

        /**
         * The pattern of the path to the image
         */
        'path_pattern' => '([a-zA-Z0-9\/]+)([a-zA-Z0-9]+.(png|jpg|jpeg))'
    ],

    'shortcuts' => [
        'default' => [

        ]
    ],
];