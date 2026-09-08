<?php

namespace JeffersonGoncalves\Hubspot;

use JeffersonGoncalves\Hubspot\Resources\CrmObject;

/**
 * Entry point for CRM objects, form submissions, and marketing emails.
 */
class Hubspot
{
    public function __construct(
        protected HubspotClient $client,
    ) {}

    /**
     * Any CRM object type: contacts, companies, deals, tickets, products,
     * line_items, or a custom object.
     */
    public function crm(string $objectType): CrmObject
    {
        return new CrmObject($this->client, $objectType);
    }

    public function contacts(): CrmObject
    {
        return $this->crm('contacts');
    }

    public function companies(): CrmObject
    {
        return $this->crm('companies');
    }

    public function deals(): CrmObject
    {
        return $this->crm('deals');
    }

    public function tickets(): CrmObject
    {
        return $this->crm('tickets');
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<int|string, mixed>
     */
    public function formSubmissions(string $formGuid, array $query = []): array
    {
        return $this->client->get("/form-integrations/v1/submissions/forms/{$formGuid}", $query);
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<int|string, mixed>
     */
    public function marketingEmails(array $query = []): array
    {
        return $this->client->get('/marketing/v3/emails', $query);
    }

    public function client(): HubspotClient
    {
        return $this->client;
    }
}
