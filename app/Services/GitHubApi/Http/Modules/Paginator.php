<?php

namespace App\Services\GitHubApi\Http\Modules;

use App\Services\GitHubApi\Http\HttpClient;
use App\Services\GitHubApi\Abstracts\HttpModule;
use App\Services\GitHubApi\Contracts\HandlesResponse;
use Illuminate\Support\Str;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;


class Paginator extends HttpModule implements HandlesResponse
{
    public function __construct(private HttpClient $client) {}

    public function handleResponse(ResponseInterface $response, string $url): ResponseInterface
    {
        $body = [];
        $next = $url;
        Log::info("Paginator called " . $response->getStatusCode());

        do {
            $data = json_decode((string) $response->getBody(), true) ?? [];
            $body = array_merge($body, $data);
            $next = $this->nextUrl($response);

            if ($next) {
                Log::info("Paginator next: $next");
                $cacheUrl = str_replace(rtrim($this->client->baseUrl, '/'), '', $next);
                [$qurl, $query] = explode('?', $next, 2);
                $response = $this->client->prepareClient($cacheUrl, $this->className)->get($qurl, $query);
            }
        } while ($next);

        return $response->withBody(
            Utils::streamFor(json_encode($body))
        );
    }
    private function nextUrl($response): ?string
    {
        if (!$response->hasHeader('link')) {
            return null;
        }

        foreach (explode(', ', $response->getHeaderLine('link')) as $link) {
            if (str_contains($link, 'rel="next"')) {
                return Str::between($link, '<', '>');
            }
        }

        return null;
    }
}
