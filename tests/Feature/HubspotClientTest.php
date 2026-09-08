<?php

use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Hubspot\Exceptions\HubspotException;
use JeffersonGoncalves\Hubspot\Facades\Hubspot;

it('sends the private app token as a bearer header', function () {
    Http::fake(['api.hubapi.com/*' => Http::response(['results' => []])]);

    Hubspot::contacts()->all();

    Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer test-token'));
});

it('throws a HubspotException carrying the API error body', function () {
    Http::fake([
        'api.hubapi.com/*' => Http::response([
            'status' => 'error',
            'message' => 'Invalid input JSON',
            'category' => 'VALIDATION_ERROR',
        ], 400),
    ]);

    try {
        Hubspot::contacts()->create([]);
        $this->fail('Expected a HubspotException.');
    } catch (HubspotException $e) {
        expect($e->getMessage())->toBe('Invalid input JSON')
            ->and($e->getCode())->toBe(400)
            ->and($e->errorBody()['category'])->toBe('VALIDATION_ERROR');
    }
});

it('falls back to a generic message when the body has none', function () {
    Http::fake(['api.hubapi.com/*' => Http::response('', 500)]);

    expect(fn () => Hubspot::contacts()->all())
        ->toThrow(HubspotException::class, 'HubSpot API error (HTTP 500).');
});

it('retries a 429 before giving up', function () {
    Http::fake([
        'api.hubapi.com/*' => Http::sequence()
            ->push(['message' => 'rate limited'], 429)
            ->push(['results' => []], 200),
    ]);

    $result = Hubspot::contacts()->all();

    expect($result['results'])->toBe([]);
    Http::assertSentCount(2);
});

it('reads form submissions and marketing emails', function () {
    Http::fake([
        'api.hubapi.com/form-integrations/v1/submissions/forms/*' => Http::response(['results' => ['a']]),
        'api.hubapi.com/marketing/v3/emails*' => Http::response(['results' => ['b']]),
    ]);

    expect(Hubspot::formSubmissions('guid-1')['results'])->toBe(['a'])
        ->and(Hubspot::marketingEmails(['limit' => 10])['results'])->toBe(['b']);
});
