<?php

use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Hubspot\Facades\Hubspot;

it('lists records', function () {
    Http::fake([
        'api.hubapi.com/crm/v3/objects/contacts*' => Http::response(['results' => [['id' => '1']]]),
    ]);

    $result = Hubspot::contacts()->all(['limit' => 10]);

    expect($result['results'][0]['id'])->toBe('1');

    Http::assertSent(fn ($request) => $request['limit'] === 10);
});

it('finds a record by id with selected properties', function () {
    Http::fake([
        'api.hubapi.com/crm/v3/objects/contacts/1*' => Http::response(['id' => '1', 'properties' => ['email' => 'user@example.com']]),
    ]);

    $result = Hubspot::contacts()->find('1', ['email', 'firstname']);

    expect($result['properties']['email'])->toBe('user@example.com');

    Http::assertSent(fn ($request) => $request['properties'] === 'email,firstname');
});

it('wraps the payload of a create in properties', function () {
    Http::fake([
        'api.hubapi.com/crm/v3/objects/contacts' => Http::response(['id' => '1'], 201),
    ]);

    $result = Hubspot::contacts()->create(['email' => 'user@example.com']);

    expect($result['id'])->toBe('1');

    Http::assertSent(fn ($request) => $request['properties'] === ['email' => 'user@example.com']);
});

it('patches a record on update', function () {
    Http::fake([
        'api.hubapi.com/crm/v3/objects/contacts/1' => Http::response(['id' => '1']),
    ]);

    Hubspot::contacts()->update('1', ['lifecyclestage' => 'customer']);

    Http::assertSent(function ($request) {
        return $request->method() === 'PATCH'
            && $request['properties'] === ['lifecyclestage' => 'customer'];
    });
});

it('deletes a record', function () {
    Http::fake([
        'api.hubapi.com/crm/v3/objects/contacts/1' => Http::response(null, 204),
    ]);

    Hubspot::contacts()->delete('1');

    Http::assertSent(fn ($request) => $request->method() === 'DELETE');
});

it('builds a single-property search payload', function () {
    Http::fake([
        'api.hubapi.com/crm/v3/objects/contacts/search' => Http::response(['total' => 1, 'results' => []]),
    ]);

    Hubspot::contacts()->searchBy('email', 'user@example.com', properties: ['email']);

    Http::assertSent(function ($request) {
        return $request['filterGroups'] === [[
            'filters' => [[
                'propertyName' => 'email',
                'operator' => 'EQ',
                'value' => 'user@example.com',
            ]],
        ]]
            && $request['properties'] === ['email'];
    });
});

it('omits the properties key when none are requested in a search', function () {
    Http::fake([
        'api.hubapi.com/crm/v3/objects/contacts/search' => Http::response(['total' => 0, 'results' => []]),
    ]);

    Hubspot::contacts()->searchBy('email', 'user@example.com');

    Http::assertSent(fn ($request) => ! array_key_exists('properties', (array) $request->data()));
});

it('associates two records with a PUT', function () {
    Http::fake([
        'api.hubapi.com/crm/v3/objects/deals/*' => Http::response([]),
    ]);

    Hubspot::deals()->associate('10', 'contacts', '1', 'deal_to_contact');

    Http::assertSent(function ($request) {
        return $request->method() === 'PUT'
            && str_contains((string) $request->url(), '/crm/v3/objects/deals/10/associations/contacts/1/deal_to_contact');
    });
});

it('reads associations of a record', function () {
    Http::fake([
        'api.hubapi.com/crm/v3/objects/deals/10/associations/contacts*' => Http::response(['results' => []]),
    ]);

    Hubspot::deals()->associations('10', 'contacts');

    Http::assertSent(fn ($request) => $request->method() === 'GET');
});

it('targets any object type through crm()', function () {
    Http::fake([
        'api.hubapi.com/crm/v3/objects/p_custom*' => Http::response(['results' => []]),
    ]);

    Hubspot::crm('p_custom')->all();

    Http::assertSent(fn ($request) => str_contains((string) $request->url(), '/crm/v3/objects/p_custom'));
});
