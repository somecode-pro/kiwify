<?php

namespace Somecode\Restify\Support\Interfaces;

use Somecode\OpenApi\Builder;

interface Applieble
{
    public function apply(Builder $builder): Builder;
}
