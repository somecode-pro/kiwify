<?php

namespace Somecode\Restify\Support\MethodExtensions;

use Somecode\Restify\Support\Extractors\ValidationRules\ValidationRulesExtractor;
use Somecode\Restify\Support\Routes\RouteData;

class RequestBodyExtension implements Extension
{
    public function apply(RouteData $route): void
    {
        $validationRulesExtractor = new ValidationRulesExtractor($route);
        $validationRulesExtractor->extract();
    }
}
