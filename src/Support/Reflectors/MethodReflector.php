<?php

namespace Somecode\Restify\Support\Reflectors;

use ReflectionMethod;

class MethodReflector
{
    public function __construct(
        private string $className,
        private string $name
    ) {}

    public static function create(string $className, string $name): static
    {
        return new static($className, $name);
    }

    public function getReflection(): ReflectionMethod
    {
        return new ReflectionMethod($this->className, $this->name);
    }
}
