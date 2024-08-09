<?php

namespace Somecode\Restify\Support\Mappers;

use PhpParser\Node;
use PhpParser\Node\ArrayItem;
use Somecode\Restify\Support\Extractors\Node\NodeValue;

class ArrayNodeItemsMapper
{
    public function __construct(
        /** @var Node\ArrayItem[] */
        private array $nodes
    ) {}

    public function toValidationRules(): array
    {
        $rules = collect();

        foreach ($this->nodes as $node) {
            if (! $node->value instanceof Node\Expr\Array_ && ! $node->value instanceof Node\Scalar\String_) {
                continue;
            }

            $fieldRules = $node->value instanceof Node\Expr\Array_
                ? $node->value->items
                : explode('|', $node->value->value);

            // todo: may be need numbers also
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
            ->map(function (Node\ArrayItem|string $item) {
                return is_string($item) ? $item : NodeValue::extract($item->value);
            })
            ->filter(fn (mixed $value) => ! is_null($value))
            ->toArray();
    }
}
