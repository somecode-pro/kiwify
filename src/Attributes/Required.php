<?php

namespace Somecode\Restify\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_ALL)]
class Required
{
    public function __construct(
        public bool $required = true
    ) {}
}
