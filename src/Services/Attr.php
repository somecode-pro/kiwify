<?php

namespace Somecode\Restify\Services;

use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

class Attr
{
    public function __construct(
        private ReflectionMethod|ReflectionParameter|ReflectionClass $reflection,
        private string $attribute,
        private \Closure $callback
    ) {}

    public static function handle(ReflectionMethod|ReflectionParameter|ReflectionClass $reflection, string $attribute, \Closure $callback): void
    {
        $instance = new static($reflection, $attribute, $callback);

        $instance->handleAll();
    }

    public function handleAll(): void
    {
        foreach ($this->reflection->getAttributes($this->attribute) as $attribute) {
            $attributeInstance = $attribute->newInstance();

            ($this->callback)($attributeInstance);
        }
    }
}
