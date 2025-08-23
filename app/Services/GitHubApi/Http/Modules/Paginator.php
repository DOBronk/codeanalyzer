<?php

namespace App\Services\GitHubApi\Http\Modules;

use App\Services\GitHubApi\Http\HttpClient;
use App\Services\GitHubApi\Abstracts\HttpModule;
use App\Services\GitHubApi\Contracts\HandlesResponse;
use Illuminate\Support\Str;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

class Paginator extends HttpModule implements HandlesResponse
{
    public function __construct(private HttpClient $client) {}

    public function handleResponse(ResponseInterface $response, string $url): ResponseInterface
    {
        $body = [];
        $next = $url;

        do {
            $response = $this->makeHeader($response, $next);
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

    // Workaround for if response is an etag match and cached result
    private function makeHeader($response, string $url)
    {
        if ($response->hasHeader('link') || !str_contains($url, 'page')) {
            return $response;
        }

        Log::info("Paginator cached url");

        preg_match('/(?<!_)page=(\d+)/', $url, $matches);
        $next = isset($matches[1]) ? ((int) $matches[1] + 1) : 2;
        $nextUrl = preg_replace('/(?<!_)page=\d+/', "page={$next}", $url);

        return $response->withAddedHeader('link', "<{$nextUrl}>; rel=\"next\"");
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
