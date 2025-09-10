<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CORS Paths
    |--------------------------------------------------------------------------
    |
    | Here you may configure the paths that are exposed to CORS requests.
    | You may use wildcards, for example: `api/*`.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    /*
    |--------------------------------------------------------------------------
    | CORS Allowed Methods
    |--------------------------------------------------------------------------
    |
    | Here you may configure the methods that are allowed to be used in
    | CORS requests. You may use `['*']` to allow all methods.
    |
    */

    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | CORS Allowed Origins
    |--------------------------------------------------------------------------
    |
    | Here you may configure the origins that are allowed to make CORS
    | requests to your application. You can use `['*']` to allow
    | all origins.
    |
    */

    'allowed_origins' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | CORS Allowed Origins Patterns
    |--------------------------------------------------------------------------
    |
    | Here you may configure the origins that are allowed to make CORS
    | requests to your application using a regular expression.
    |
    */

    'allowed_origins_patterns' => [],

    /*
    |--------------------------------------------------------------------------
    | CORS Allowed Headers
    |--------------------------------------------------------------------------
    |
    | Here you may configure the headers that are allowed to be sent in
    | CORS requests. You can use `['*']` to allow all headers.
    |
    */

    'allowed_headers' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | CORS Exposed Headers
    |--------------------------------------------------------------------------
    |
    | Here you may configure the headers that are exposed to the browser
    | in CORS responses.
    |
    */

    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | CORS Max Age
    |--------------------------------------------------------------------------
    |
    | Here you may configure the number of seconds the browser should
    | cache the results of a preflight request. A value of `0` will
    | disable the cache.
    |
    */

    'max_age' => 0,

    /*
    |--------------------------------------------------------------------------
    | CORS Supports Credentials
    |--------------------------------------------------------------------------
    |
    | Here you may configure whether the browser should send credentials
    | (cookies, authorization headers, etc.) with CORS requests.
    |
    */

    'supports_credentials' => false,

];
