<?php

namespace Somecode\Restify\Providers;

use Illuminate\Support\ServiceProvider;
use Somecode\Restify\Console\Commands\BuildCommand;
use Somecode\Restify\Services\RestifyService;

class RestifyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->bind('restify', RestifyService::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                BuildCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/../../config/restify.php' => config_path('restify.php'),
        ]);
    }

    public function register() {}
}
