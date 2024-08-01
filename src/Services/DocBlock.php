<?php

namespace Somecode\Restify\Services;

use Illuminate\Support\Collection;
use phpDocumentor\Reflection\DocBlock as ReflectionDocBlock;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlockFactory;

class DocBlock
{
    private ReflectionDocBlock $docBlock;

    public function __construct(
        public ?string $docComment
    ) {
        $this->docBlock = DocBlockFactory::createInstance()->create($docComment);
    }

    public static function create(?string $docComment): static
    {
        return new static($docComment);
    }

    public function hasTag(array|string ...$tagNames): bool
    {
        if (is_array($tagNames[0])) {
            $tagNames = $tagNames[0];
        }

        foreach ($tagNames as $tagName) {
            if ($this->docBlock->hasTag($tagName)) {
                return true;
            }
        }

        return false;
    }

    public function getTagValues(array|string ...$tagNames): Collection
    {
        if (is_array($tagNames[0])) {
            $tagNames = $tagNames[0];
        }

        $tags = collect();

        foreach ($tagNames as $tagName) {
            $tags = $tags->concat($this->docBlock->getTagsByName($tagName));
        }

        return $tags->map(
            fn (Tag $tag) => $tag->getDescription()->getBodyTemplate()
        );
    }

    public function getSummary(): ?string
    {
        return $this->docBlock->getSummary();
    }
}
