<?php

namespace Somecode\Restify\Providers;

use Illuminate\Support\ServiceProvider;
use Somecode\Restify\Console\Commands\BuildCommand;
use Somecode\Restify\Services\RestifyService;

class RestifyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->services();
        $this->console();
        $this->resources();
    }

    public function register() {}

    private function services(): void
    {
        $this->app->bind('restify', RestifyService::class);
    }

    private function console(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                BuildCommand::class,
            ]);
        }
    }

    private function resources(): void
    {
        $this->publishes([
            __DIR__.'/../../config/restify.php' => config_path('restify.php'),
        ], 'restify');
    }
}
