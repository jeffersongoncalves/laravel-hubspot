<?php

namespace Jeffersongoncalves\Hubspot\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Jeffersongoncalves\Hubspot\Hubspot
 */
class Hubspot extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-hubspot';
    }
}
