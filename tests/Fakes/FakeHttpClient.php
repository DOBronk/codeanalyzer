<?php
/*
namespace Tests\Fakes;

use App\Services\GitHubApi\Http\HttpClient;
use Illuminate\Http\Client\PendingRequest;
use Psr\Http\Message\ResponseInterface;

class FakeHttpClient extends HttpClient
{
    /** @var array<string, ResponseInterface> */
/*    private array $responses = [];

    public function __construct(private string $baseUrl = 'https://api.github.com')
    {
        // Don’t call parent::__construct to avoid real setup
    }

    public function prepareClient(string $url, string $className): PendingRequest
    {
        // Return self since we don’t actually need a real client
        return $this;
    }

    public function get(string $url, string $query = ''): ResponseInterface
    {
        $fullUrl = $url . ($query ? '?' . $query : '');
        return $this->responses[$fullUrl] ?? throw new \RuntimeException("No fake response for [$fullUrl]");
    }

    public function addFakeResponse(string $url, ResponseInterface $response): void
    {
        $this->responses[$url] = $response;
    }
} */