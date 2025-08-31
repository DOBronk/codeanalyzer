<?php

namespace App\Services\GitHubApi\Http\Modules;

use App\Services\GitHubApi\Http\HttpClient;
use App\Services\GitHubApi\Abstracts\HttpModule;
use App\Services\GitHubApi\Contracts\HandlesFinal;
use App\Services\GitHubApi\Contracts\HandlesResponse;
use GuzzleHttp\Promise;
use Illuminate\Support\Str;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;

class Paginator extends HttpModule implements HandlesResponse
{
    private static $body;
    private int $pages;
    private int $page_responses;
    private static bool $ignore;

    public function __construct(private HttpClient $client)
    {
        self::$body = [];
        self::$ignore = false;
    }

    // Keep per_page preferably high, github has some resources which will automatically paginate at a given number. Often these
    // can be atleast doubled. Keep the amount of pages low so that they won't exceed 10 for performance and resources. 
    public function handleResponse(ResponseInterface $response, string $url): ResponseInterface
    {
        if (self::$ignore) {
            self::$body = array_merge(self::$body, json_decode((string) $response->getBody(), true));
            Log::info("Paginator ignored further request, async mode");
            return $response;
        }

        self::$body = json_decode((string) $response->getBody(), true);
        $next = $this->nextUrl($response);
        $this->pages = $this->getPageCount($next);

        if ($this->pages === 1) {
            return $response;
        }

        Log::info("Paginator total pages {$this->pages} next link: {$next[0]}");
        $this->page_responses = 1; //Already have the first one

        [$qurl, $query] = explode('?', $next[0], 2);
        $promises = [];


        self::$ignore = true;
        for ($i = 2; $i <= $this->pages; $i++) {
            $newquery = preg_replace('/(?<!_)page=(\d+)/', "page=$i", $query);
            Log::info("Paginator next query string : $newquery");
            $promises[] = $this->client->prepareClient()->async()->get($qurl, $newquery);
        }

        //  } while ($next);
        Log::info("Paginator waiting");
        $responses = Promise\Utils::settle($promises)->wait();
        Log::info("Paginator Finished");
        self::$ignore = false;
        $response = $response->withoutHeader('link');
        // ->then(fn($respond) => $this->processResponse($respond))
        return $response->withBody(Utils::streamFor(json_encode(self::$body)));
    }

    /*  public function handleFinal(Response $respond): Response
    {
        if ($respond->getStatusCode() === 250) {
            do {
                sleep(1);
                Log::info("Paginator waiting");
            } while ($this->pages > $this->page_responses);
            $respond = $respond->withBody(Utils::streamFor(json_encode($this->body)));
        } else return $respond;
        return new Response($respond);
    } */

    private function processResponse(ResponseInterface $response)
    {
        Log::info("Paginator async response");

        $data = json_decode((string) $response->getBody(), true) ?? [];
        $this->body = array_merge($this->body, $data);
        Log::info("Paginator page responses {$this->page_responses}");
        $this->page_responses++;
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
