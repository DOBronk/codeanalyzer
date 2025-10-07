<?php

namespace App\Services\GitHubApi\Http\Modules;

use App\Services\GitHubApi\Contracts\HandlesRequest;
use App\Services\GitHubApi\Contracts\HandlesResponse;
use App\Services\GitHubApi\Abstracts\HttpModule;
use App\Services\GitHubApi\Contracts\HandlesTransferStats;
use App\Services\GitHubApi\Settings;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\TransferStats;
use Psr\Http\Message\UriInterface;

class EtagCache extends HttpModule implements HandlesRequest, HandlesTransferStats, HandlesResponse
{
    private $responses;

    public function __construct(private Settings $settings)
    {
        $this->responses = [];
    }

    public function handleRequest(RequestInterface $request): RequestInterface
    {
        $key = $this->genKey($request->getUri());

        if (Cache::has($key)) {
            $tag = $this->convertEtag(Cache::get($key));
            $query = $request->getUri()->getQuery();
            $headers = Cache::get($tag)['headers'];
            // First response of a paginated result without querying a paginated page number should be ignored! 
            if (!(key_exists('link', $headers) && !preg_match('/(?<!_)page=(\d+)/', $query, $matches))) {
                $request = $request->withAddedHeader('if-none-match', $tag);
            }
        }

        return $request;
    }

    public function handleTransferStats(TransferStats $stats)
    {
        if ($stats->hasResponse() && $stats->getResponse()->hasHeader('X-Github-Request-Id')) {
            $url = $this->convertUri($stats->getEffectiveUri());
            $this->responses[$stats->getResponse()->getHeaderLine('X-Github-Request-Id')] = $url;
        }
    }

    public function handleResponse(ResponseInterface $response): ResponseInterface
    {
        $status = $response->getStatusCode();

        if ($status < 400 && $response->hasHeader('etag') && $response->hasHeader('X-Github-Request-Id')) {
            $etag = $this->convertEtag($response->getHeaderLine('etag'));
            $ghid = $response->getHeaderLine('X-Github-Request-Id');
            $url = $this->responses[$ghid];
            $body = (string) $response->getBody();
            unset($this->responses[$ghid], $ghid);

            if ($status === 304 && Cache::has($etag)) {
                $data = Cache::get($etag);
                foreach ($data['headers'] as $name => $values) {
                    $response = $response->withAddedHeader($name, $values);
                }
                $response = $response->withStatus($data['status'])->withBody(Utils::streamFor($data['body']));
                Log::debug("Etag module: Served {$url} via cache!");
            } else if ($status < 300 && !empty($body)) {
                Cache::put($etag, ['body' => $body, 'status' => $status, 'headers' => $response->getHeaders()], $this->settings->cache_timeout + 30); // Keep value data 30 seconds longer for request
                Cache::put($this->genKey($url), $etag, $this->settings->cache_timeout);
                Log::debug("Etag response: $etag is put in cache!");
            } else {
                Log::error("Etag module: Key $etag in cache, but result not. Nothing to serve!");
            }
        }

        return $response;
    }

    private function convertEtag(string $etag)
    {
        return substr($etag, 0, 2) === 'W/' ? substr($etag, 2) : $etag;
    }

    private function convertUri(UriInterface $uri): string
    {
        $query = $uri->getQuery();
        return $uri->getPath() . ($query ? "?$query" : '');
    }
    private function genKey(UriInterface|string $url)
    {
        $url = $url instanceof UriInterface ? $this->convertUri($url) : $url;
        return "{$this->settings->apiKey}{$url}";
    }
}
