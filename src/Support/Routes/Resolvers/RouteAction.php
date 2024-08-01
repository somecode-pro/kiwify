<?php

namespace Somecode\Restify\Support\Routes\Resolvers;

use Illuminate\Routing\Route;
use ReflectionMethod;

trait RouteAction
{
    private ReflectionMethod $action;

    protected function action(): ReflectionMethod
    {
        return $this->action;
    }

    protected function getRouteAction(Route $route): void
    {
        $action = $route->getActionName();

        if (str_contains($action, '@')) {
            [$controller, $method] = explode('@', $action);
        } elseif (class_exists($action)) {
            $controller = $action;
            $method = '__invoke';
        }

        $controller = $controller ?? null;
        $action = $method ?? null;

        if ($controller && $action) {
            $this->action = new ReflectionMethod($controller, $action);
        }
    }
}