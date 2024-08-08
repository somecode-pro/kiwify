<?php

namespace Somecode\Restify\Support\Extractors\Parameter;

use Illuminate\Routing\Route;
use ReflectionMethod;
use Somecode\OpenApi\Entities\Parameter\PathParameter;

class ParametersExtractor
{
    public function __construct(
        private Route $route,
        private ReflectionMethod $action
    ) {}

    public function parameters(): array
    {
        return array_merge($this->getPathParameters());
    }

    /**
     * @return array<PathParameter>
     */
    private function getPathParameters(): array
    {
        return (new Path($this->route, $this->action))->getParameters();
    }
}
