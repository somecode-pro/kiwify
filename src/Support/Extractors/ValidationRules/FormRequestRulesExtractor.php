<?php

namespace Somecode\Restify\Support\Extractors\ValidationRules;

use Illuminate\Foundation\Http\FormRequest;
use PhpParser\Node;
use ReflectionNamedType;
use ReflectionParameter;
use Somecode\Restify\Services\NodeFinder;
use Somecode\Restify\Support\Helpers\NodeFilters;
use Somecode\Restify\Support\Reflectors\MethodReflector;

readonly class FormRequestRulesExtractor
{
    public function __construct(
        private MethodReflector $reflector
    ) {}

    public function extract(): ?ExtractedRulesResult
    {
        $rules = $this->getRules();

        if (empty($rules)) {
            return null;
        }

        return new ExtractedRulesResult(
            rules: $rules,
            nodes: $this->getNodes()
        );
    }

    private function getRules(): array
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

    private function getNodes(): array
    {
        $requestClassName = $this->getFormRequestClassFromMethodParameters();

        if (is_null($requestClassName)) {
            return [];
        }

        $reflector = MethodReflector::create($requestClassName, 'rules');

        $astNode = $reflector->getAstNode();

        $result = [];

        $nodes = NodeFinder::find($astNode, [new NodeFilters, 'arrayItemsWithDocBlock']);

        /** @var Node\ArrayItem $node */
        foreach ($nodes as $node) {
            $result[$node->key->value] = $node;
        }

        return $result;
    }

    public function getFormRequestClassFromMethodParameters(): ?string
    {
        $reflection = $this->reflector->getReflection();

        /** @var ReflectionParameter $parameter */
        $parameter = collect($reflection->getParameters())->first(function (ReflectionParameter $parameter) {
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
