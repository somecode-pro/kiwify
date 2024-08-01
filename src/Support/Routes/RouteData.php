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

readonly class RouteData
{
    public function __construct(
        public Route $route
    ) {}

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

        //

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
}
