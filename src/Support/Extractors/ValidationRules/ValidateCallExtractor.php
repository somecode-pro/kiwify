<?php

namespace Somecode\Restify\Support\Extractors\ValidationRules;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node;
use Somecode\Restify\Services\NodeFinder;
use Somecode\Restify\Support\Mappers\ArrayNodeItemsMapper;
use Somecode\Restify\Support\Reflectors\MethodReflector;

readonly class ValidateCallExtractor
{
    public function __construct(
        private MethodReflector $reflector
    ) {}

    public function extract(): ?ExtractedRulesResult
    {
        $methodNode = $this->reflector->getAstNode();

        $findValidationRulesCriteria = [
            $this->getValidationCriteriaForRequest(),
            $this->getValidationCriteriaForThis(),
            $this->getValidationCriteriaForStaticCall(),
        ];

        foreach ($findValidationRulesCriteria as $criteria) {
            $validationRules = $this->findValidationRules($methodNode, $criteria);

            if (! is_null($validationRules)) {
                break;
            }
        }

        $validationRules = $this->extractArrayItemsFromArg($validationRules);

        if (is_null($validationRules)) {
            return null;
        }

        $arrayItemsWithDocBlock = collect($validationRules)
            ->filter(fn (Node\ArrayItem $item) => ! is_null($item->getDocComment()));

        $nodes = [];

        /** @var Node\ArrayItem $item */
        foreach ($arrayItemsWithDocBlock as $item) {
            $nodes[$item->key->value] = $item;
        }

        return new ExtractedRulesResult(
            rules: (new ArrayNodeItemsMapper($validationRules))->toValidationRules(),
            nodes: $nodes
        );
    }

    private function getPossibleParamType(Node\Stmt\ClassMethod $methodNode, Node\Expr\Variable $node): ?string
    {
        $paramsMap = collect($methodNode->getParams())
            ->mapWithKeys(function (Node\Param $param) {
                if (! isset($param->type->name)) {
                    return [];
                }

                return [
                    $param->var->name => $param->type->name,
                ];
            })
            ->toArray();

        return $paramsMap[$node->name] ?? null;
    }

    private function findValidationRules($methodNode, array $criteria): ?Node\Arg
    {
        $validationCall = NodeFinder::findOne($methodNode, function (Node $node) use ($criteria) {
            return $this->matchesCriteria($node, $criteria);
        });

        return $validationCall->args[$criteria['paramIndex']] ?? null;
    }

    private function matchesCriteria(Node $node, array $criteria): bool
    {
        if ($node instanceof $criteria['nodeType']) {
            if (isset($criteria['varType']) && ! ($node->var instanceof $criteria['varType'])) {
                return false;
            }

            if (isset($criteria['varName']) && $node->var->name !== $criteria['varName']) {
                return false;
            }

            if (isset($criteria['classType']) && ! ($node->class instanceof $criteria['classType'])) {
                return false;
            }

            if (isset($criteria['className']) && ! is_a($node->class->toString(), $criteria['className'], true)) {
                return false;
            }

            if (isset($criteria['methodName']) && $node->name->name !== $criteria['methodName']) {
                return false;
            }

            if (isset($criteria['paramIndex']) && ! isset($node->args[$criteria['paramIndex']])) {
                return false;
            }

            if (isset($criteria['requestCheck']) && ! $criteria['requestCheck']($node)) {
                return false;
            }

            return true;
        }

        return false;
    }

    private function extractArrayItemsFromArg($node): ?array
    {
        $node = $node instanceof Node\Arg ? $node->value : $node;

        return $node instanceof Node\Expr\Array_ ? $node->items : null;
    }

    /**
     * $request->validate($rules), when $request param is Request instance
     */
    private function getValidationCriteriaForRequest(): array
    {
        return [
            'nodeType' => Node\Expr\MethodCall::class,
            'varType' => Node\Expr\Variable::class,
            'methodName' => 'validate',
            'paramIndex' => 0,
            'requestCheck' => function (Node $node) {
                return is_a($this->getPossibleParamType($this->reflector->getAstNode(), $node->var), Request::class, true);
            },
        ];
    }

    /**
     * $this->validate($request, $rules), when using ValidatesRequests trait
     */
    private function getValidationCriteriaForThis(): array
    {
        return [
            'nodeType' => Node\Expr\MethodCall::class,
            'varType' => Node\Expr\Variable::class,
            'varName' => 'this',
            'methodName' => 'validate',
            'paramIndex' => 1,
            'requestCheck' => function (Node $node) {
                return $node->args[0]->value instanceof Node\Expr\Variable
                    && is_a($this->getPossibleParamType($this->reflector->getAstNode(), $node->args[0]->value), Request::class, true);
            },
        ];
    }

    /**
     * Validator::make($request->all(), $rules)
     */
    private function getValidationCriteriaForStaticCall(): array
    {
        return [
            'nodeType' => Node\Expr\StaticCall::class,
            'classType' => Node\Name::class,
            'className' => Validator::class,
            'methodName' => 'make',
            'paramIndex' => 1,
            'requestCheck' => function (Node $node) {
                return $node->args[0]->value instanceof Node\Expr\MethodCall
                    && is_a($this->getPossibleParamType($this->reflector->getAstNode(), $node->args[0]->value->var), Request::class, true);
            },
        ];
    }
}
