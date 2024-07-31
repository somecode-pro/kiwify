<?php

return [
    'routes' => 'api',
    'domain' => null,
    'path' => resource_path('swagger/openapi.json'),
    'title' => env('API_TITLE'),
    'description' => env('API_DESCRIPTION'),
    'version' => env('API_VERSION', '1.0.0'),

    /*
    |--------------------------------------------------------------------------
    | API Servers
    |--------------------------------------------------------------------------
    |
    | You can add a list of servers (hosts) that you can switch between
    | in the Swagger interface.
    |
    | For example:
    |
    | 'servers' => [
    |     [
    |         'url' => 'https://{env}.domain.com',
    |         'description' => 'Host for {env} environment',
    |         'variables' => [
    |             [
    |                 'name' => 'env',
    |                 'description' => 'Environment',
    |                 'default' => 'dev',
    |                 'enum' => ['dev', 'prod'],
    |             ]
    |         ]
    |     ]
    | ]
    |
    | More details in the Restify documentation (@url)
    |
    */

    'servers' => [],
];
