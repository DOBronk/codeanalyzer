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
    public function handleResponse(ResponseInterface $response, $params = null): ResponseInterface
    {
        $next = $this->nextUrl($response);

        // Only gather all paginated pages in one response if the next page is 2
        if (!empty($next) && $this->getPageNumber($next[0]) === 2) {
            $pages = $this->getPageCount($next);
            $promises = $this->createPaginatedRequests($next[0], $pages, $params[0]);

            Log::debug(sprintf("Paginator: Waiting for %s pages to be fetched", $pages - 1));
            $promised = Promise\Utils::unwrap($promises);
            Log::debug("Paginator: Fetching completed");
            $body = json_decode((string) $response->getBody(), true);
            $merge_type = array_is_list($body) ? 'array_merge' : 'array_merge_recursive';

            foreach ($promised as $promise) {
                $body = $merge_type($body, json_decode((string) $promise->getBody(), true));
            }

            $body = json_encode($body);
            $response = $response->withBody(Utils::streamFor($body));
            Log::debug("Paginator: Assembly completed");
        }

        return $response;
    }

    private function createPaginatedRequests(string $nextUrl, int $pages, HttpClient $client): array
    {
        [$url, $query] = explode('?', urldecode($nextUrl), 2);

        $client = $client->prepareGuzzle();

        for ($i = 2; $i <= $pages; $i++) {
            $newquery = preg_replace('/(?<=[&\?]|^)page=(\d+)/', "page=$i", $query);
            $promises[] = $client->getAsync("$url?$newquery");
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
        if (preg_match('/(?<=[&\?]|^)page=(\d+)/', $url, $matches)) {
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

        return empty($next) ? [1] : [$next];
    }
}
