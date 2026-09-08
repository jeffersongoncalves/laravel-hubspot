<?php

namespace JeffersonGoncalves\Hubspot;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Hubspot\Exceptions\HubspotException;
use Throwable;

/**
 * Thin wrapper around Laravel's Http client for the HubSpot API.
 *
 * Authenticates with a private app access token and retries on 429, since
 * HubSpot caps requests at 100 per 10 seconds.
 *
 * ponytail: private app token only, no OAuth 2.0 authorization-code flow —
 * add if the package ever needs to act for third-party portals.
 */
class HubspotClient
{
    public function __construct(
        protected string $token,
        protected string $baseUrl,
        protected int $retryTimes = 3,
        protected int $retryDelay = 1000,
    ) {}

    /**
     * @param  array<string, mixed>  $query
     * @return array<int|string, mixed>
     */
    public function get(string $path, array $query = []): array
    {
        return $this->request('get', $path, ['query' => $query]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int|string, mixed>
     */
    public function post(string $path, array $data = []): array
    {
        return $this->request('post', $path, ['json' => $data]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int|string, mixed>
     */
    public function patch(string $path, array $data = []): array
    {
        return $this->request('patch', $path, ['json' => $data]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int|string, mixed>
     */
    public function put(string $path, array $data = []): array
    {
        return $this->request('put', $path, ['json' => $data]);
    }

    /** @return array<int|string, mixed> */
    public function delete(string $path): array
    {
        return $this->request('delete', $path);
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<int|string, mixed>
     */
    protected function request(string $method, string $path, array $options = []): array
    {
        $response = $this->pendingRequest()
            ->send(strtoupper($method), $path, $options);

        if ($response->failed()) {
            throw HubspotException::fromResponse($response);
        }

        return (array) ($response->json() ?? []);
    }

    protected function pendingRequest(): PendingRequest
    {
        return Http::withToken($this->token)
            ->acceptJson()
            ->baseUrl($this->baseUrl)
            ->retry($this->retryTimes, $this->retryDelay, function (Throwable $e): bool {
                return $e instanceof RequestException && $e->response->status() === 429;
            }, throw: false);
    }
}
