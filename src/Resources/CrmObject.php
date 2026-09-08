<?php

namespace JeffersonGoncalves\Hubspot\Resources;

use JeffersonGoncalves\Hubspot\HubspotClient;

/**
 * CRUD, search, and associations for any CRM object type: contacts,
 * companies, deals, tickets, products, line_items, or a custom object.
 */
class CrmObject
{
    public function __construct(
        protected HubspotClient $client,
        protected string $type,
    ) {}

    /**
     * @param  array<string, mixed>  $query
     * @return array<int|string, mixed>
     */
    public function all(array $query = []): array
    {
        return $this->client->get("/crm/v3/objects/{$this->type}", $query);
    }

    /**
     * @param  string[]  $properties
     * @return array<int|string, mixed>
     */
    public function find(string $id, array $properties = []): array
    {
        $query = $properties === [] ? [] : ['properties' => implode(',', $properties)];

        return $this->client->get("/crm/v3/objects/{$this->type}/{$id}", $query);
    }

    /**
     * @param  array<string, mixed>  $properties
     * @return array<int|string, mixed>
     */
    public function create(array $properties): array
    {
        return $this->client->post("/crm/v3/objects/{$this->type}", ['properties' => $properties]);
    }

    /**
     * @param  array<string, mixed>  $properties
     * @return array<int|string, mixed>
     */
    public function update(string $id, array $properties): array
    {
        return $this->client->patch("/crm/v3/objects/{$this->type}/{$id}", ['properties' => $properties]);
    }

    public function delete(string $id): void
    {
        $this->client->delete("/crm/v3/objects/{$this->type}/{$id}");
    }

    /**
     * Raw search payload: filterGroups, sorts, properties, limit, after.
     *
     * @param  array<string, mixed>  $payload
     * @return array<int|string, mixed>
     */
    public function search(array $payload): array
    {
        return $this->client->post("/crm/v3/objects/{$this->type}/search", $payload);
    }

    /**
     * Search on a single property, the common case.
     *
     * @param  string[]  $properties  properties to return
     * @return array<int|string, mixed>
     */
    public function searchBy(string $property, string $value, string $operator = 'EQ', array $properties = []): array
    {
        $payload = [
            'filterGroups' => [[
                'filters' => [[
                    'propertyName' => $property,
                    'operator' => $operator,
                    'value' => $value,
                ]],
            ]],
        ];

        if ($properties !== []) {
            $payload['properties'] = $properties;
        }

        return $this->search($payload);
    }

    /**
     * Associate this record with a record of another object type.
     *
     * @return array<int|string, mixed>
     */
    public function associate(string $id, string $toObjectType, string $toId, string $associationType): array
    {
        return $this->client->put(
            "/crm/v3/objects/{$this->type}/{$id}/associations/{$toObjectType}/{$toId}/{$associationType}"
        );
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<int|string, mixed>
     */
    public function associations(string $id, string $toObjectType, array $query = []): array
    {
        return $this->client->get("/crm/v3/objects/{$this->type}/{$id}/associations/{$toObjectType}", $query);
    }
}
