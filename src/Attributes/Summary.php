<?php

namespace Somecode\Restify\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_ALL)]
class Summary
{
    public function __construct(
        public string $summary
    ) {}
}
