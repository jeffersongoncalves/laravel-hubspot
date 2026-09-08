<?php

namespace JeffersonGoncalves\Hubspot\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JeffersonGoncalves\Hubspot\Hubspot
 */
class Hubspot extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \JeffersonGoncalves\Hubspot\Hubspot::class;
    }
}
