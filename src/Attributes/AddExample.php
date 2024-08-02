<?php

namespace Somecode\Restify\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_ALL | Attribute::IS_REPEATABLE)]
class AddExample
{
    public function __construct(
        public string $name,
        public mixed $value
    ) {}
}
