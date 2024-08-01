<?php

namespace Somecode\Restify\Support\Routes\Resolvers;

use ReflectionMethod;
use Somecode\Restify\Attributes\Summary;
use Somecode\Restify\Services\DocBlock;

trait MethodSummary
{
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
