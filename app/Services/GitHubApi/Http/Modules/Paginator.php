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
    private static $body;
    private static bool $ignore;

    public function __construct(private HttpClient $client)
    {
        self::$ignore = false;
    }
    public function handleResponse(ResponseInterface $response): ResponseInterface
    {
        if (self::$ignore) {
            self::$body = array_merge(self::$body, json_decode((string) $response->getBody(), true));
            return $response;
        }

        self::$body = json_decode((string) $response->getBody(), true);
        $next = $this->nextUrl($response);

        if (($pages = $this->getPageCount($next)) === 1) {
            return $response;
        }

        self::$ignore = true;
        Log::info("Paginator total pages: {$pages} next link: {$next[0]}");
        $promises = $this->createPaginatedRequests($next[0], $pages);
        Log::info("Paginator waiting");
        Promise\Utils::settle($promises)->wait();
        Log::info("Paginator Finished");
        self::$ignore = false;

        return $response->withBody(Utils::streamFor(json_encode(self::$body)));
    }

    private function createPaginatedRequests(string $nextUrl, int $pages): array
    {
        $promises = [];
        [$qurl, $query] = explode('?', $nextUrl, 2);

        for ($i = 2; $i <= $pages; $i++) {
            $newquery = preg_replace('/(?<!_)page=(\d+)/', "page=$i", $query);
            $promises[] = $this->client->prepareClient()->async()->get($qurl, $newquery);
        }

        return $promises;
    }

    private function getPageCount(string|array|null $nextResult): int
    {
        if (is_array($nextResult)) {
            if (preg_match('/(?<!_)page=(\d+)/', $nextResult[1], $matches)) {
                return (int) $matches[1];
            } else {
                return 1;
            }
        }

        return isset($nextResult) ? 2 : 1;
    }

    private function nextUrl($response): string|array|null
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

        return $next ?? null;
    }
}
