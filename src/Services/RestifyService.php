<?php

namespace Somecode\Restify\Services;

use Illuminate\Support\Facades\File;
use Somecode\OpenApi\Builder;
use Somecode\Restify\Support\Handlers\ServerHandler;

class RestifyService
{
    private Builder $builder;

    public function __construct()
    {
        $this->builder = new Builder(
            config('restify.title'),
            config('restify.version'),
            config('restify.description'),
        );
    }

    public function build(): void
    {
        (new ServerHandler())($this->builder);

        dump($this->builder->toArray());

        if (! File::exists(config('restify.path'))) {
            File::makeDirectory(dirname(config('restify.path')), 0755, true);
        }

        File::put(config('restify.path'), $this->builder->toJson());
    }
}
