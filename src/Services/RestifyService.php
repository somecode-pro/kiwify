<?php

namespace Somecode\Restify\Services;

use Illuminate\Support\Facades\File;
use Somecode\OpenApi\Builder;
use Somecode\Restify\Support\Applicants\RoutesApplicant;
use Somecode\Restify\Support\Applicants\ServersApplicant;
use Somecode\Restify\Support\MethodExtensions\DescriptionExtension;
use Somecode\Restify\Support\MethodExtensions\ParametersExtension;
use Somecode\Restify\Support\MethodExtensions\RequestBodyExtension;
use Somecode\Restify\Support\MethodExtensions\SummaryExtension;
use Somecode\Restify\Support\MethodExtensions\TagsExtension;

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
        (new ServersApplicant)($this->builder);
        (new RoutesApplicant)($this->builder);

        dump($this->builder->toArray());

        if (! File::exists(config('restify.path'))) {
            File::makeDirectory(dirname(config('restify.path')), 0755, true);
        }

        File::put(config('restify.path'), $this->builder->toJson());
    }

    public function extensions(): array
    {
        return [
            TagsExtension::class,
            SummaryExtension::class,
            DescriptionExtension::class,
            ParametersExtension::class,
            RequestBodyExtension::class,
        ];
    }
}
