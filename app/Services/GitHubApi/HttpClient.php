<?php

namespace App\Services\GitHubApi;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use app\Services\GitHubApi\Contracts\ApiSettingsInterface;
use App\Exceptions\AuthorizationException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Log;

// Laravel HTTP Client Handler, in toekomst misschien interface met adapter design pattern 
// toevoegen om composer package van te maken die op andere HTTP clients kan werken)
class HttpClient
{
    private $settings;
    private $headers = [];

    public function __construct(ApiSettingsInterface $settings)
    {
        $this->settings = $settings;
    }
    public function get(string $uri, array|string|null $query = null)
    {
        Log::info("Get url", ['url' => "{$this->settings->getUrl()}{$uri}"]);

        return $this->handleResponse($this->prepareClient()->get("{$this->settings->getUrl()}{$uri}", $query));
    }
    public function post(string $uri, array $data = [])
    {
        Log::info("Posted data", ['url' => "{$this->settings->getUrl()}{$uri}"]);

        return $this->handleResponse($this->prepareClient()->post("{$this->settings->getUrl()}{$uri}", $data));
    }
    private function handleResponse(Response $response)
    {
        if (!$response->successful()) {
            throw $response->unauthorized() ? new AuthorizationException() : $response->toException();
        }

        return  $response->noContent() ? null : $response->json();
    }
    private function prepareClient(): PendingRequest
    {
        return Http::withToken($this->settings->getKey());
    }
}
