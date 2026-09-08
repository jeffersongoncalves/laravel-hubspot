<?php

namespace JeffersonGoncalves\Hubspot\Tests;

use JeffersonGoncalves\Hubspot\HubspotServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            HubspotServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('hubspot.token', 'test-token');
        $app['config']->set('hubspot.base_url', 'https://api.hubapi.com');
        $app['config']->set('hubspot.retry_times', 2);
        $app['config']->set('hubspot.retry_delay', 0);
    }
}
