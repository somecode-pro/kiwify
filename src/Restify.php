<?php

namespace Somecode\Restify;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void build()
 *
 * @see \Somecode\Restify\Services\RestifyService
 */
class Restify extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'restify';
    }
}
