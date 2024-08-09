<?php

namespace Somecode\Restify\Support\Helpers;

use PhpParser\Node;

class NodeFilters
{
    public function arrayItems(Node $node): bool
    {
        return $node instanceof Node\Expr\ArrayItem && $node->key instanceof Node\Scalar\String_;
    }

    public function arrayItemsWithDocBlock(Node $node): bool
    {
        return $this->arrayItems($node) && ! is_null($node->getDocComment());
    }
}
