<?php

namespace Jeffersongoncalves\Hubspot\Tests;

use Jeffersongoncalves\Hubspot\HubspotServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            HubspotServiceProvider::class,
        ];
    }
}
