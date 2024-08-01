<?php

namespace Somecode\Restify\Support\Applicants;

use Somecode\OpenApi\Builder;
use Somecode\Restify\Support\Interfaces\Applicable;
use Somecode\Restify\Support\Routes\Router;

class RoutesApplicant implements Applicable
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();
    }

    public function __invoke(Builder $builder): Builder
    {
        return $this->apply($builder);
    }

    public function apply(Builder $builder): Builder
    {
        $paths = $this->router->getPaths();

        return $builder->addPaths($paths->toArray());
    }
}
