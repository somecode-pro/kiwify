<?php

namespace Somecode\Restify\Support\Extractors\Node;

use PhpParser\Node;
use ReflectionClass;
use ReflectionException;

class NodeValue
{
    public function __construct(
        private Node $node
    ) {}

    public static function extract(Node $node)
    {
        $instance = new static($node);

        return $instance->getNodeValue();
    }

    /**
     * @throws \Exception
     */
    public function getNodeValue()
    {
        return $this->resolveExpression($this->node);
    }

    /**
     * @throws \Exception
     */
    private function resolveExpression(Node $node)
    {
        if ($node instanceof Node\Scalar\String_) {
            return $node->value;
        }

        if ($node instanceof Node\Scalar\LNumber) {
            return $node->value;
        }

        if ($node instanceof Node\Scalar\DNumber) {
            return $node->value;
        }

        if ($node instanceof Node\Expr\ConstFetch) {
            return constant($node->name->toString());
        }

        if ($node instanceof Node\Expr\ClassConstFetch) {
            $className = $node->class->toString();
            $constName = $node->name->toString();

            if ($constName === 'class') {
                return $className;
            }

            return constant("$className::$constName");
        }

        if ($node instanceof Node\Expr\Array_) {
            $result = [];
            foreach ($node->items as $item) {
                $result[] = $this->resolveExpression($item->value);
            }

            return $result;
        }

        if ($node instanceof Node\Expr\New_) {
            return $this->createInstanceFromNewExpr($node);
        }

        if ($node instanceof Node\Expr\StaticCall) {
            return $this->callStaticMethod($node);
        }

        throw new \Exception('Unsupported expression type: '.get_class($node));
    }

    /**
     * @throws ReflectionException
     * @throws \Exception
     */
    private function createInstanceFromNewExpr(Node\Expr\New_ $node)
    {
        $className = $node->class->toString();

        $args = [];

        foreach ($node->args as $arg) {
            $args[] = $this->resolveExpression($arg->value);
        }

        $reflectionClass = new ReflectionClass($className);

        return $reflectionClass->newInstanceArgs($args);
    }

    /**
     * @throws \Exception
     */
    private function callStaticMethod(Node\Expr\StaticCall $node)
    {
        $className = $node->class->toString();
        $methodName = $node->name->toString();
        $args = [];
        foreach ($node->args as $arg) {
            $args[] = $this->resolveExpression($arg->value);
        }

        try {
            $reflectionClass = new ReflectionClass($className);
            if (! $reflectionClass->hasMethod($methodName)) {
                throw new \Exception("Метод $methodName не существует в классе $className.");
            }
            $reflectionMethod = $reflectionClass->getMethod($methodName);
            if (! $reflectionMethod->isStatic()) {
                throw new \Exception("Метод $methodName в классе $className не является статическим.");
            }

            return $reflectionMethod->invokeArgs(null, $args);
        } catch (ReflectionException $e) {
            throw new \Exception('Ошибка при вызове метода: '.$e->getMessage());
        }
    }
}
