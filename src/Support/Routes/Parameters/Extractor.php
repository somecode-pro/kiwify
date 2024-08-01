<?php

namespace Somecode\Restify\Support\Routes\Parameters;

use Illuminate\Routing\Route;
use ReflectionMethod;

class Extractor
{
    public function __construct(
        private Route   $route,
        private ReflectionMethod $action
    ) {}

    public function parameters(): array
    {
        $this->getPathParameters();

        return [];
    }

    private function getPathParameters()
    {
        $pathParametersExtractor = new Path($this->route, $this->action);

        $pathParametersExtractor->parameters();
    }
}
