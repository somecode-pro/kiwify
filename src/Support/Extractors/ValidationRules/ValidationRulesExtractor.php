<?php

namespace Somecode\Restify\Support\Extractors\ValidationRules;

use Somecode\Restify\Support\Routes\RouteData;

class ValidationRulesExtractor
{
    public function __construct(
        private RouteData $route
    ) {}

    public function extract()
    {
        $reflector = $this->route->getMethodReflector();
        $reflectionMethod = $reflector->getReflection();

        $formRequestRulesExtractor = new FormRequestRulesExtractor($reflector->getReflection());

        $rules = $formRequestRulesExtractor->getRules();

        dump($rules);

        $nodes = $formRequestRulesExtractor->getNodes();

        // dump($rules, $nodes);
    }
}
