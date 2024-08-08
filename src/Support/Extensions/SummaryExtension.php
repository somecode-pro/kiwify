<?php

namespace Somecode\Restify\Support\Extensions;

use ReflectionMethod;
use Somecode\Restify\Attributes\Summary;
use Somecode\Restify\Exceptions\RouteMethodNotSupported;
use Somecode\Restify\Services\DocBlock;
use Somecode\Restify\Support\Routes\RouteData;

class SummaryExtension implements Extension
{
    /**
     * @throws RouteMethodNotSupported
     */
    public function apply(RouteData $route): void
    {
        $method = $route->getSpecificationMethodInstance();
        $reflector = $route->getMethodReflector()->getReflection();

        $summary = $this->getSummary($reflector);

        if (is_string($summary)) {
            $method->summary($summary);
        }
    }

    public function getSummary(ReflectionMethod $action): ?string
    {
        return $this->getSummaryFromAttributes($action) ?? $this->getSummaryFromDocBlock($action);
    }

    private function getSummaryFromAttributes(ReflectionMethod $action): ?string
    {
        foreach ($action->getAttributes(Summary::class) as $attribute) {
            /** @var Summary $attributeInstance */
            $attributeInstance = $attribute->newInstance();

            return $attributeInstance->summary;
        }

        return null;
    }

    private function getSummaryFromDocBlock(ReflectionMethod $action): ?string
    {
        $docComment = $action->getDocComment();

        if ($docComment === false) {
            return null;
        }

        $docBlock = DocBlock::create($docComment);

        return $docBlock->summary();
    }
}
