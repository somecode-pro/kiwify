<?php

namespace Somecode\Restify\Support\Extractors\ValidationRules;

use Illuminate\Foundation\Http\FormRequest;
use PhpParser\Node;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use Somecode\Restify\Services\NodeFinder;
use Somecode\Restify\Support\Helpers\NodeFilters;
use Somecode\Restify\Support\Mappers\ArrayNodeMapper;
use Somecode\Restify\Support\Reflectors\MethodReflector;

class FormRequestRulesExtractor
{
    public function __construct(
        private ReflectionMethod $reflectionMethod
    ) {}

    public function extract() {}

    public function getRules(): array
    {
        $requestClassName = $this->getFormRequestClassFromMethodParameters();

        if (is_null($requestClassName)) {
            return [];
        }

        /** @var FormRequest $request */
        $request = new $requestClassName;

        if (method_exists($request, 'rules')) {
            return $request->rules();
        }

        return [];
    }

    public function getNodes()
    {
        $requestClassName = $this->getFormRequestClassFromMethodParameters();

        if (is_null($requestClassName)) {
            return [];
        }

        $reflector = MethodReflector::create($requestClassName, 'rules');

        $astNode = $reflector->getAstNode();

        $nodes = NodeFinder::find($astNode, [new NodeFilters, 'arrayItems']);

        $arrayNodeMapper = new ArrayNodeMapper($nodes);

        dd(
            $arrayNodeMapper->toValidationRules()
        );

        $nodes = collect($nodes)->map(function (Node\ArrayItem $item) {
            return [
                'key' => $item->key->value,
                'value' => collect($item->value->items)->map(function ($item) {
                    return $this->getNodeValue($item);
                })->toArray(),
            ];
        });

        dd($nodes->toArray());
    }

    private function getNodeValue($node)
    {
        if ($node->value instanceof Node\Expr\StaticCall) {
            return dd($node->value);
        }

        return null;
    }

    public function getFormRequestClassFromMethodParameters(): ?string
    {
        /** @var ReflectionParameter $parameter */
        $parameter = collect($this->reflectionMethod->getParameters())->first(function (ReflectionParameter $parameter) {
            if ($parameter->hasType()) {
                $type = $parameter->getType();

                return $type instanceof ReflectionNamedType
                    && is_string($type->getName())
                    && class_exists($type->getName())
                    && is_subclass_of($type->getName(), FormRequest::class);
            }

            return false;
        });

        if (! is_null($parameter)) {
            return $parameter->getType()->getName();
        }

        return null;
    }
}
