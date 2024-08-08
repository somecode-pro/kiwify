<?php

namespace Somecode\Restify\Support\Extensions;

use Somecode\Restify\Support\Routes\RouteData;

interface Extension
{
    public function apply(RouteData $route): void;
}
