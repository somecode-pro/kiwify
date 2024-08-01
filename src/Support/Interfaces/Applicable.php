<?php

namespace Somecode\Restify\Support\Interfaces;

use Somecode\OpenApi\Builder;

interface Applicable
{
    public function __invoke(Builder $builder): Builder;

    public function apply(Builder $builder): Builder;
}
