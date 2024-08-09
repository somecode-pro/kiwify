<?php

namespace Somecode\Restify\Services;

use Illuminate\Support\Arr;
use PhpParser\Node\Stmt\ClassMethod;

readonly class NodeFinder
{
    public function __construct(
        private ?ClassMethod $astNode,
    ) {}

    public static function find(?ClassMethod $astNode, callable $filter): array
    {
        $instance = new static($astNode);

        return $instance->findNodes($filter);
    }

    public static function findOne(?ClassMethod $astNode, callable $filter): ?\PhpParser\Node
    {
        $instance = new static($astNode);

        return $instance->findFirstNode($filter);
    }

    private function findNodes(callable $filter): array
    {
        if (! $this->astNode) {
            return [];
        }

        return (new \PhpParser\NodeFinder)->find(
            Arr::wrap($this->astNode->getStmts()), $filter
        );
    }

    private function findFirstNode(callable $filter): ?\PhpParser\Node
    {
        if (! $this->astNode) {
            return null;
        }

        return (new \PhpParser\NodeFinder)->findFirst($this->astNode, $filter);
    }
}
