<?php

use JeffersonGoncalves\Hubspot\Facades\Hubspot;
use JeffersonGoncalves\Hubspot\Hubspot as HubspotManager;

it('merges the default config', function () {
    expect(config('hubspot.base_url'))->toBe('https://api.hubapi.com');
});

it('resolves the facade to the manager singleton', function () {
    expect(Hubspot::getFacadeRoot())->toBeInstanceOf(HubspotManager::class);
});

it('always resolves the same manager instance', function () {
    expect(app(HubspotManager::class))->toBe(app(HubspotManager::class));
});
