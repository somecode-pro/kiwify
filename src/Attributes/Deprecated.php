<?php

namespace Somecode\Restify\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_ALL)]
class Deprecated
{
    public function __construct(
        public bool $deprecated = true
    ) {}
}
