<?php

namespace Somecode\Restify\Support\Routes;

use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use Somecode\OpenApi\Entities\Method\Delete;
use Somecode\OpenApi\Entities\Method\Get;
use Somecode\OpenApi\Entities\Method\Method;
use Somecode\OpenApi\Entities\Method\Patch;
use Somecode\OpenApi\Entities\Method\Post;
use Somecode\OpenApi\Entities\Method\Put;
use Somecode\Restify\Exceptions\RouteMethodNotSupported;
use Somecode\Restify\Support\Reflectors\MethodReflector;

class RouteData
{
    private Method $specificationMethod;

    private string $controllerClassName;

    private string $actionMethodName;

    /**
     * @throws RouteMethodNotSupported
     */
    public function __construct(
        public readonly Route $route
    ) {
        $this->getControllerAndMethod();
        $this->specificationMethod = $this->getSpecificationMethodInstance();
    }

    public function uri(): string
    {
        return $this->route->uri();
    }

    public function method(): string
    {
        return Arr::first($this->route->methods());
    }

    public function route(): Route
    {
        return $this->route;
    }

    public function getMethodReflector(): MethodReflector
    {
        return MethodReflector::create($this->controllerClassName, $this->actionMethodName);
    }

    /**
     * @throws RouteMethodNotSupported
     */
    public function getSpecificationMethodInstance(): Method
    {
        return $this->specificationMethod ??= match ($this->method()) {
            'GET' => Get::create(),
            'POST' => Post::create(),
            'PUT' => Put::create(),
            'PATCH' => Patch::create(),
            'DELETE' => Delete::create(),
            default => throw new RouteMethodNotSupported("Method '{$this->method()}' not supported"),
        };
    }

    private function getControllerAndMethod(): void
    {
        $action = $this->route->getActionName();

        if (str_contains($action, '@')) {
            [$controller, $method] = explode('@', $action);
        } elseif (class_exists($action)) {
            $controller = $action;
            $method = '__invoke';
        }

        $controller = $controller ?? null;
        $action = $method ?? null;

        if ($controller && $action) {
            $this->controllerClassName = $controller;
            $this->actionMethodName = $action;
        }
    }
}
