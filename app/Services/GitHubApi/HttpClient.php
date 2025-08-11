<?php

namespace App\Services\GitHubApi;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use App\Services\GithubService;
use App\Exceptions\AuthorizationException;
use Illuminate\Http\Client\PendingRequest;

// Laravel HTTP Client Handler, in toekomst misschien interface met adapter design pattern 
// toevoegen om composer package van te maken die op andere HTTP clients kan werken)
class HttpClient
{
    // Parent voor ophalen key (kan tussentijds wijzigen)
    private $service;
    private $headers = [];

    public function __construct(GitHubService $git, private string $url)
    {
        $this->service = $git;
    }
    public function get(string $uri, array|string|null $query = null)
    {
        return $this->handleResponse($this->prepareClient()->get("{$this->url}$uri", $query));
    }
    public function post(string $uri, array $data = [])
    {
        return $this->handleResponse($this->prepareClient()->post("{$this->url}$uri", $data));
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
        return Http::withToken($this->service->getKey());
    }
}
