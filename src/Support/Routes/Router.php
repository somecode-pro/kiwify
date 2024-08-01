<?php

namespace Somecode\Restify\Support\Routes;

use Illuminate\Routing\Route as RouteInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Somecode\OpenApi\Entities\Path;

class Router
{
    /** @var Collection<RouteData> */
    private Collection $routes;

    public function __construct()
    {
        $this->routes = $this->getRoutesCollection();
    }

    /**
     * @return Collection<RouteData>
     */
    public function routes(): Collection
    {
        return $this->routes;
    }

    /**
     * @return Collection<Path>
     *
     * @throws \Exception
     */
    public function getPaths(): Collection
    {
        $paths = collect();

        foreach ($this->routes as $route) {
            if (! $paths->has($route->uri())) {
                $paths->put($route->uri(), Path::create($route->uri()));
            }

            /** @var Path $path */
            $path = $paths->get($route->uri());

            $path->addMethod($route->methodInstance());
        }

        return $paths->values();
    }

    private function getRoutesCollection(): Collection
    {
        return $this->getFilteredRoutes()->map(
            fn (RouteInfo $route) => new RouteData($route)
        );
    }

    /**
     * @return Collection<RouteInfo[]>
     */
    private function getFilteredRoutes(): Collection
    {
        $routes = Arr::where(
            Route::getRoutes()->getRoutes(),
            fn (RouteInfo $route) => $this->isValidRoute($route)
        );

        return collect($routes);
    }

    private function isValidRoute(RouteInfo $route): bool
    {
        return str_starts_with($route->uri(), $this->routesPrefix());
    }

    private function routesPrefix(): ?string
    {
        return config('restify.routes');
    }
}
