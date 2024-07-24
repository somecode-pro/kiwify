<?php

namespace Somecode\Restify\Providers;

use Illuminate\Support\ServiceProvider;

class RestifyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/restify.php' => config_path('restify.php'),
        ]);
    }

    public function register() {}
}
