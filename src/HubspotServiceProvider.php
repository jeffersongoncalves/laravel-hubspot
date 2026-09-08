<?php

namespace JeffersonGoncalves\Hubspot;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class HubspotServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('hubspot')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(HubspotClient::class, function () {
            return new HubspotClient(
                token: (string) config('hubspot.token'),
                baseUrl: (string) config('hubspot.base_url'),
                retryTimes: (int) config('hubspot.retry_times'),
                retryDelay: (int) config('hubspot.retry_delay'),
            );
        });

        $this->app->singleton(Hubspot::class, function ($app) {
            return new Hubspot($app->make(HubspotClient::class));
        });
    }
}
