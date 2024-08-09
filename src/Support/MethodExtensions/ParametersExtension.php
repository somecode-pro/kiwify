<?php

namespace Somecode\Restify\Support\MethodExtensions;

use Somecode\Restify\Exceptions\RouteMethodNotSupported;
use Somecode\Restify\Support\Extractors\Parameter\ParametersExtractor;
use Somecode\Restify\Support\Routes\RouteData;

class ParametersExtension implements Extension
{
    /**
     * @throws RouteMethodNotSupported
     */
    public function apply(RouteData $route): void
    {
        $method = $route->getSpecificationMethodInstance();
        $reflection = $route->getMethodReflector()->getReflection();

        $extractor = new ParametersExtractor($route->route(), $reflection);

        $method->addParameters($extractor->parameters());
    }
}
