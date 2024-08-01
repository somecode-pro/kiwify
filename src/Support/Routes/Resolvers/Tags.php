<?php

namespace Somecode\Restify\Support\Routes\Resolvers;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Somecode\Restify\Attributes\Tags as TagsAttribute;
use ReflectionMethod;
use Somecode\Restify\Services\DocBlock;

trait Tags
{
    public function getRouteTags(ReflectionMethod $action): array
    {
        $tags = $this->getTagsFromAttributes($action);

        $tags = array_merge($tags, $this->getTagsFromDocBlock($action));
        $tags = array_unique($tags);
        $tags = array_values($tags);

        return count($tags) === 0 ? [$this->getTagByControllerName($action)] : $tags;
    }

    private function getTagsFromAttributes(ReflectionMethod $action): array
    {
        $controller = new \ReflectionClass($action->class);

        $attributes = array_merge(
            $controller->getAttributes(TagsAttribute::class),
            $action->getAttributes(TagsAttribute::class)
        );

        $tags = [];

        foreach ($attributes as $attribute) {
            /** @var TagsAttribute $attributeInstance */
            $attributeInstance = $attribute->newInstance();

            $tags = array_merge($tags, $attributeInstance->tags);
        }

        return $tags;
    }

    private function getTagsFromDocBlock(ReflectionMethod $action): array
    {
        $docComment = $action->getDocComment();

        if ($docComment === false) {
            return [];
        }

        $docBlock = DocBlock::create($docComment);

        $names = ['tag', 'tags'];

        if ($docBlock->hasTag($names)) {
            $values = $docBlock->getTagValues($names);

            return $this->prepareTagValues($values);
        }

        return [];
    }

    private function prepareTagValues(Collection $values): array
    {
        $preparedValues = $values->map(
            fn ($tag) => array_map('trim', explode(',', $tag))
        );

        return Arr::flatten($preparedValues);
    }

    private function getTagByControllerName(ReflectionMethod $action): string
    {
        $controller = new \ReflectionClass($action->class);

        return config('restify.tags.ignore_controller_prefix') === true
            ? str_replace('Controller', '', $controller->getShortName())
            : $controller->getShortName();
    }
}