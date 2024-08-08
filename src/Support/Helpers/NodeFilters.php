<?php

namespace Somecode\Restify\Support\Helpers;

use PhpParser\Node;

class NodeFilters
{
    public function arrayItems(Node $node): bool
    {
        return $node instanceof Node\Expr\ArrayItem && $node->key instanceof Node\Scalar\String_;
    }
}
