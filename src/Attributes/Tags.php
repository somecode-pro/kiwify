<?php

namespace Somecode\Restify\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_ALL)]
class Tags
{
    public array $tags;

    public function __construct(
        ...$tags
    ) {
        if (count($tags) === 1 && is_array($tags[0])) {
            $tags = $tags[0];
        }

        $this->tags = $tags;
    }
}