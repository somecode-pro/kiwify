<?php

namespace Somecode\Restify\Support\MethodExtensions;

use Somecode\Restify\Support\Routes\RouteData;

interface Extension
{
    public function apply(RouteData $route): void;
}
