<?php

namespace Jeffersongoncalves\Hubspot;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class HubspotServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-hubspot')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations();
    }
}
