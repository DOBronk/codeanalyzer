<?php

namespace App\Services\GitHubApi\Http\Modules;

use App\Services\GitHubApi\Http\HttpClient;
use App\Services\GitHubApi\Abstracts\HttpModule;
use App\Services\GitHubApi\Contracts\HandlesResponse;
use GuzzleHttp\Promise;
use Illuminate\Support\Str;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Support\Facades\Log;

class Paginator extends HttpModule implements HandlesResponse
{
    public function __construct(private HttpClient $client) {}
    public function handleResponse(ResponseInterface $response): ResponseInterface
    {
        $next = $this->nextUrl($response);

        // If the next page is higher than 2 return response (either a single page is requested or paginator is working)
        if (empty($next) || $this->getPageNumber($next[0]) <> 2) {
            return $response;
        }

        $pages = $this->getPageCount($next);
        $promises = $this->createPaginatedRequests($next[0], $pages);

        Log::info(sprintf("Paginator: Waiting for %s pages to be fetched", $pages - 1));
        $promised = Promise\Utils::unwrap($promises);
        Log::info("Paginator: Fetching completed, assembling final request");
        $body = json_decode((string) $response->getBody(), true);

        foreach ($promised as $promise) {
            $body = array_merge_recursive($body, json_decode((string) $promise->getBody(), true));
        }

        $body = json_encode($body);
        return $response->withBody(Utils::streamFor($body));
    }

    private function createPaginatedRequests(string $nextUrl, int $pages): array
    {
        [$url, $query] = explode('?', urldecode($nextUrl), 2);

        for ($i = 2; $i <= $pages; $i++) {
            $newquery = preg_replace('/(?<!_)page=(\d+)/', "page=$i", $query);
            $promises[] = $this->client->prepareClient()->async()->get($url, $newquery);
        }

        return $promises;
    }

    private function getPageCount(array $nextResult): int
    {
        if (count($nextResult) < 2) {
            return 2; // Return 2 if github API can't calculate the total amount of pages
        }

        return $this->getPageNumber($nextResult[1]);
    }

    private function getPageNumber(string $url): int
    {
        if (preg_match('/(?<!_)page=(\d+)/', $url, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }
    private function nextUrl($response): ?array
    {
        if (!$response->hasHeader('link')) {
            return null;
        }

        foreach (explode(', ', $response->getHeaderLine('link')) as $link) {
            if (preg_match('/rel="([^"]+)"/', $link, $matches)) {
                ${$matches[1]} = Str::between($link, '<', '>');
            }

            if (isset($next, $last)) {
                return [$next, $last];
            }
        }

        return empty($next) ? null : [$next];
    }
}
