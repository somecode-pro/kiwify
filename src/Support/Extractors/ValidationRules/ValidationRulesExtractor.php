<?php

namespace Somecode\Restify\Support\Extractors\ValidationRules;

use Somecode\Restify\Support\Routes\RouteData;

readonly class ValidationRulesExtractor
{
    public function __construct(
        private RouteData $route
    ) {}

    public function extract(): ExtractedRulesResult
    {
        $reflector = $this->route->getMethodReflector();

        return $this->mergeResults(
            (new FormRequestRulesExtractor($reflector))->extract(),
            (new ValidateCallExtractor($reflector))->extract()
        );
    }

    private function mergeResults(?ExtractedRulesResult $formRequestResult, ?ExtractedRulesResult $validateCallResult): ExtractedRulesResult
    {
        $formRequestRules = $formRequestResult?->getRules() ?? [];
        $formRequestNodes = $formRequestResult?->getNodes() ?? [];
        $validateCallRules = $validateCallResult?->getRules() ?? [];
        $validateCallNodes = $validateCallResult?->getNodes() ?? [];

        return new ExtractedRulesResult(
            rules: array_merge($formRequestRules, $validateCallRules),
            nodes: array_merge($formRequestNodes, $validateCallNodes)
        );
    }
}
