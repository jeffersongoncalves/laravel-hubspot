<div class="filament-hidden">

![Laravel HubSpot](https://raw.githubusercontent.com/jeffersongoncalves/laravel-hubspot/main/art/jeffersongoncalves-laravel-hubspot.png)

</div>

# Laravel HubSpot

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jeffersongoncalves/laravel-hubspot.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-hubspot)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-hubspot/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/jeffersongoncalves/laravel-hubspot/actions?query=workflow%3ATests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-hubspot/pint.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/jeffersongoncalves/laravel-hubspot/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jeffersongoncalves/laravel-hubspot.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-hubspot)
[![License](https://img.shields.io/packagist/l/jeffersongoncalves/laravel-hubspot.svg?style=flat-square)](LICENSE.md)

A PHP/Laravel client for the [HubSpot](https://www.hubspot.com/) API. Read and write any CRM object — contacts, companies, deals, tickets, custom objects — plus search, associations, form submissions, and marketing emails, authenticated with a private app token.

## Features

- CRUD for any CRM object type (`/crm/v3/objects/{type}`)
- Search: raw payloads or a one-line single-property filter
- Associations: create and read them between object types
- Form submissions (`/form-integrations/v1`) and marketing emails (`/marketing/v3/emails`)
- Automatic retry on `429`, since HubSpot caps requests at 100 per 10 seconds
- Throws `HubspotException` (with the original API error body) on any non-2xx response

## Installation

You can install the package via composer:

```bash
composer require jeffersongoncalves/laravel-hubspot
```

Publish the config file:

```bash
php artisan vendor:publish --tag=hubspot-config
```

Set your private app token in `.env`:

```env
HUBSPOT_TOKEN=your-private-app-token
```

Create the token under **Settings > Integrations > Private Apps** and grant it the CRM scopes you need.

## Configuration

```php
// config/hubspot.php
return [
    'token' => env('HUBSPOT_TOKEN', ''),
    'base_url' => env('HUBSPOT_BASE_URL', 'https://api.hubapi.com'),
    'retry_times' => env('HUBSPOT_RETRY_TIMES', 3),
    'retry_delay' => env('HUBSPOT_RETRY_DELAY', 1000),
];
```

## Usage

The package is resolved via the `Hubspot` facade or by injecting `JeffersonGoncalves\Hubspot\Hubspot`.

`contacts()`, `companies()`, `deals()` and `tickets()` are shortcuts for `crm('contacts')` and friends — use `crm()` directly for `products`, `line_items`, or a custom object.

### List records

```php
use JeffersonGoncalves\Hubspot\Facades\Hubspot;

$result = Hubspot::contacts()->all(['limit' => 10]);
// $result['results']
```

### Find a record

```php
$contact = Hubspot::contacts()->find('101', ['email', 'firstname', 'lastname']);
// $contact['properties']['email']
```

### Create a record

```php
$contact = Hubspot::contacts()->create([
    'email' => 'user@example.com',
    'firstname' => 'John',
    'lastname' => 'Doe',
    'company' => 'Example Inc',
]);
// $contact['id']
```

### Update a record

```php
Hubspot::contacts()->update('101', [
    'lifecyclestage' => 'customer',
]);
```

### Delete a record

```php
Hubspot::contacts()->delete('101');
```

### Search

Single property, the common case:

```php
$result = Hubspot::contacts()->searchBy('email', 'user@example.com', properties: ['email', 'firstname']);
```

Or pass the raw payload for anything more complex:

```php
$result = Hubspot::deals()->search([
    'filterGroups' => [[
        'filters' => [
            ['propertyName' => 'dealstage', 'operator' => 'EQ', 'value' => 'appointmentscheduled'],
            ['propertyName' => 'amount', 'operator' => 'GT', 'value' => '5000'],
        ],
    ]],
    'sorts' => [['propertyName' => 'closedate', 'direction' => 'DESCENDING']],
    'limit' => 50,
]);
```

### Deals

```php
$deal = Hubspot::deals()->create([
    'dealname' => 'New Deal',
    'amount' => '10000',
    'dealstage' => 'appointmentscheduled',
    'pipeline' => 'default',
]);
```

### Associations

```php
Hubspot::deals()->associate($deal['id'], 'contacts', $contact['id'], 'deal_to_contact');

$contacts = Hubspot::deals()->associations($deal['id'], 'contacts');
```

### Custom objects

```php
$result = Hubspot::crm('p_subscriptions')->all();
```

### Form submissions and marketing emails

```php
$submissions = Hubspot::formSubmissions('form-guid');
$emails = Hubspot::marketingEmails(['limit' => 10]);
```

### Error handling

Any non-2xx API response throws `JeffersonGoncalves\Hubspot\Exceptions\HubspotException`, which exposes the decoded error body:

```php
use JeffersonGoncalves\Hubspot\Exceptions\HubspotException;

try {
    Hubspot::contacts()->find('unknown-id');
} catch (HubspotException $e) {
    logger()->error($e->getMessage(), $e->errorBody());
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email the author instead of using the issue tracker.

## Credits

- [Jefferson Gonçalves](https://github.com/jeffersongoncalves)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
