<?php

namespace Somecode\Restify\Support\Routes\Parameters;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;
use ReflectionMethod;
use ReflectionNamedType;
use function Termwind\render;

class Path
{
    public function __construct(
        private Route   $route,
        private ReflectionMethod $action
    ) {}

    public function parameters()
    {
        dump(
            $this->route->parameterNames(),
            $this->action->getParameters()
        );

        foreach ($this->action->getParameters() as $parameter) {
            if ($this->checkTypeHint($parameter->getType())) {
                continue;
            }
        }

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

    private function checkTypeHint($type): bool
    {
        if (is_null($type)) {
            return true;
        } elseif ($type instanceof ReflectionNamedType) {
            return (class_exists($type->getName()) && is_subclass_of($type->getName(), Model::class))
                || in_array($type->getName(), ['int', 'string']);
        }

        return false;
    }
}