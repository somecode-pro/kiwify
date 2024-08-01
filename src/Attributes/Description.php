<?php

namespace Somecode\Restify\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_ALL)]
class Description
{
    public function __construct(
        public string $description
    ) {}
}
