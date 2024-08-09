<?php

namespace Somecode\Restify\Support\MethodExtensions;

use ReflectionMethod;
use Somecode\Restify\Attributes\Description;
use Somecode\Restify\Exceptions\RouteMethodNotSupported;
use Somecode\Restify\Services\DocBlock;
use Somecode\Restify\Support\Routes\RouteData;

class DescriptionExtension implements Extension
{
    /**
     * @throws RouteMethodNotSupported
     */
    public function apply(RouteData $route): void
    {
        $method = $route->getSpecificationMethodInstance();
        $reflection = $route->getMethodReflector()->getReflection();

        $description = $this->getDescription($reflection);

        if (is_string($description)) {
            $method->description($description);
        }
    }

    private function getDescription(ReflectionMethod $action): ?string
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
