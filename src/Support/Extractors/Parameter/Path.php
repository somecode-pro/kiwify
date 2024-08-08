<?php

namespace Somecode\Restify\Support\Extractors\Parameter;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use Somecode\OpenApi\Entities\Parameter\Parameter;
use Somecode\OpenApi\Entities\Parameter\ParameterExample;
use Somecode\OpenApi\Entities\Parameter\PathParameter;
use Somecode\OpenApi\Entities\Schema\IntegerSchema;
use Somecode\OpenApi\Entities\Schema\Schema;
use Somecode\OpenApi\Entities\Schema\StringSchema;
use Somecode\Restify\Attributes\AddExample;
use Somecode\Restify\Attributes\Deprecated;
use Somecode\Restify\Attributes\Description;
use Somecode\Restify\Attributes\Example;
use Somecode\Restify\Attributes\Explode;
use Somecode\Restify\Attributes\Path\LabelStyle;
use Somecode\Restify\Attributes\Path\MatrixStyle;
use Somecode\Restify\Attributes\Path\SimpleStyle;
use Somecode\Restify\Services\Attr;

class Path
{
    public function __construct(
        private Route $route,
        private ReflectionMethod $action
    ) {}

    /**
     * @return array<PathParameter>
     */
    public function getParameters(): array
    {
        $parameters = $this->getParametersFromArguments();

        return $parameters->toArray();
        // Парсинг path-параметров
        // Получаем список параметров из пути $this->route->parameterNames()
        // Проходим по каждому параметру
        // В $this->action смотрим на аргументы метода
        // Смотрим, чтобы аргумент совпадал с параметром и его тип был Model::class, int или string
        // Если совпадает, то добавляем в список параметров

        // Ручное описание параметров
        // Атрибут Path(name, type = 'integer')
        // Тег @path name type (по умолчанию integer)
    }

    private function getParametersFromArguments(): Collection
    {
        $parameters = collect();

        $routeParameters = $this->route->parameterNames();
        $arguments = $this->action->getParameters();

        foreach ($arguments as $arg) {
            $argName = $arg->getName();
            $argTypeSchema = $this->getArgumentTypeSchema($arg->getType());

            if ($argTypeSchema && in_array($argName, $routeParameters)) {
                $param = PathParameter::create($argName)
                    ->schema($argTypeSchema);

                $this->applyAttributes($param, $arg);

                $parameters->push($param);
            }
        }

        return $parameters;
    }

    private function getArgumentTypeSchema($argType): false|Schema
    {
        if (is_null($argType)) {
            return IntegerSchema::create();
        }

        if ($argType instanceof ReflectionNamedType) {
            $typeHint = $argType->getName();

            return match (true) {
                $this->isModelSubclass($typeHint) => IntegerSchema::create(),
                $typeHint === 'int' => IntegerSchema::create(),
                $typeHint === 'string' => StringSchema::create(),
                default => false,
            };
        }

        return false;
    }

    private function isModelSubclass(string $typeHint): bool
    {
        return class_exists($typeHint) && is_subclass_of($typeHint, Model::class);
    }

    private function applyAttributes(Parameter $param, ReflectionParameter $arg): void
    {
        foreach ($this->defaultParameterProperties() as $attrClass => $property) {
            Attr::handle($arg, $attrClass, function ($attr) use ($param, $property) {
                $param->{$property}($attr->{$property});
            });
        }

        Attr::handle($arg, AddExample::class, function (AddExample $attr) use ($param) {
            $param->addExample(
                ParameterExample::create()
                    ->name($attr->name)
                    ->value($attr->value)
            );
        });

        foreach ($this->parameterStyles() as $style => $method) {
            Attr::handle($arg, $style, function () use ($param, $method) {
                $param->{$method}();
            });
        }
    }

    private function defaultParameterProperties(): array
    {
        return [
            Description::class => 'description',
            Example::class => 'example',
            Explode::class => 'explode',
            Deprecated::class => 'deprecated',
        ];
    }

    private function parameterStyles(): array
    {
        return [
            LabelStyle::class => 'useLabelStyle',
            MatrixStyle::class => 'useMatrixStyle',
            SimpleStyle::class => 'useSimpleStyle',
        ];
    }
}
