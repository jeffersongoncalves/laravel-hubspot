<?php

namespace JeffersonGoncalves\Hubspot\Exceptions;

use Illuminate\Http\Client\Response;
use RuntimeException;

class HubspotException extends RuntimeException
{
    /** @var array<int|string, mixed> */
    protected array $errorBody = [];

    public static function fromResponse(Response $response): self
    {
        $body = $response->json();
        $body = is_array($body) ? $body : [];

        $message = $body['message']
            ?? $body['error_description']
            ?? $body['error']
            ?? "HubSpot API error (HTTP {$response->status()}).";

        $exception = new self((string) $message, $response->status());
        $exception->errorBody = $body;

        return $exception;
    }

    /** @return array<int|string, mixed> */
    public function errorBody(): array
    {
        return $this->errorBody;
    }
}
