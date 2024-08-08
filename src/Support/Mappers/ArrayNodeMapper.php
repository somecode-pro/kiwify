<?php

namespace Somecode\Restify\Support\Mappers;

use PhpParser\Node;
use PhpParser\Node\ArrayItem;

class ArrayNodeMapper
{
    public function __construct(
        /** @var Node\ArrayItem[] */
        private array $nodes
    ) {}

    public function toValidationRules(): array
    {
        $rules = collect();

        foreach ($this->nodes as $node) {
            if (! $node->value instanceof Node\Expr\Array_) {
                continue;
            }

            $fieldRules = $node->value->items;

            if (! $node->key instanceof Node\Scalar\String_) {
                continue;
            }

            $rules->put($node->key->value, $this->getFiledRules($fieldRules));
        }

        return $rules->toArray();
    }

    /**
     * @param  array<ArrayItem>  $items
     */
    private function getFiledRules(array $items): array
    {
        return collect($items)
            ->map(function (Node\ArrayItem $item) {
                return $this->getRuleValueFromNode($item->value);
            })
            ->filter(fn (mixed $value) => ! is_null($value))
            ->toArray();
    }

    private function getRuleValueFromNode($item)
    {
        if ($item instanceof Node\Scalar\String_) {
            return $item->value;
        } elseif ($item instanceof Node\Expr\New_) {
            return $this->resolveClassInstance($item);
        }

        return null;
    }

    private function resolveClassInstance(Node\Expr\New_ $item)
    {
        $className = $item->class->name;
        $args = $item->getArgs();

        dd($className, $args[0]);
    }
}
