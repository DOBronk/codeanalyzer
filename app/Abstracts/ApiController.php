<?php

namespace App\Abstracts;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use App\Exceptions\AuthorizationException;

abstract class ApiController
{
    public function __construct(protected $apiKey) {}

    public function setApiKey(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }
    public function get(string $uri, array|string|null $query = null)
    {
        return $this->handleResponse(Http::withToken($this->apiKey)->get($uri, $query));
    }

    public function post(string $uri, array $data = [])
    {
        return $this->handleResponse(Http::withToken($this->apiKey)->post($uri, $data));
    }

    protected function handleResponse(Response $response)
    {
        if (!$response->successful()) {
            throw $response->unauthorized() ? new AuthorizationException() : $response->toException();
        }

        return  $response->noContent() ? null : $response->json();
    }
}
