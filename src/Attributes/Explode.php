<?php

namespace Somecode\Restify\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_ALL)]
class Explode
{
    public function __construct(
        public bool $explode = true
    ) {}
}
