<?php

namespace Somecode\Restify\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_ALL)]
class Example
{
    public function __construct(
        public mixed $example
    ) {}
}
