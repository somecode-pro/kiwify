<?php

namespace Somecode\Restify\Support\Routes\Resolvers;

use ReflectionMethod;
use Somecode\Restify\Attributes\Description;
use Somecode\Restify\Services\DocBlock;

trait MethodDescription
{
    public function getDescription(ReflectionMethod $action): ?string
    {
        return $this->getDescriptionFromAttributes($action) ?? $this->getDescriptionFromDocBlock($action);
    }

    private function getDescriptionFromAttributes(ReflectionMethod $action): ?string
    {
        foreach ($action->getAttributes(Description::class) as $attribute) {
            /** @var Description $attributeInstance */
            $attributeInstance = $attribute->newInstance();

            return $attributeInstance->description;
        }

        return null;
    }

    private function getDescriptionFromDocBlock(ReflectionMethod $action): ?string
    {
        $docComment = $action->getDocComment();

        if ($docComment === false) {
            return null;
        }

        $docBlock = DocBlock::create($docComment);

        $description = $docBlock->description();

        return ! empty($description) ? $description : null;
    }
}
