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
use Somecode\Restify\Support\Routes\Resolvers\RouteAction;
use Somecode\Restify\Support\Routes\Resolvers\Tags;

class RouteData
{
    use RouteAction, Tags;

    public function __construct(
        public readonly Route $route
    ) {
        $this->getRouteAction($route);
    }

    public function uri(): string
    {
        return $this->route->uri();
    }

    public function method(): string
    {
        return Arr::first($this->route->methods());
    }

    /**
     * @throws \Exception
     */
    public function methodInstance(): Method
    {
        $method = $this->getMethodInstance();

        $method->tags($this->tags());

        return $method;
    }

    /**
     * @throws \Exception
     */
    private function getMethodInstance(): Method
    {
        return match ($this->method()) {
            'GET' => Get::create(),
            'POST' => Post::create(),
            'PUT' => Put::create(),
            'PATCH' => Patch::create(),
            'DELETE' => Delete::create(),
            default => throw new \Exception('Method not supported'),
        };
    }

    private function tags(): array
    {
        return $this->getRouteTags($this->action());
    }
}
