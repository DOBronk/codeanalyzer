<?php

namespace App\Services\GitHubApi\Http\Modules;

use App\Services\GitHubApi\Contracts\HandlesRequest;
use App\Services\GitHubApi\Contracts\HandlesResponse;
use App\Services\GitHubApi\Traits\GlobalSettings;
use App\Services\GitHubApi\Abstracts\HttpModule;
use App\Services\GitHubApi\Contracts\HandlesTransferStats;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\TransferStats;
use Psr\Http\Message\UriInterface;

class EtagCache extends HttpModule implements HandlesRequest, HandlesTransferStats, HandlesResponse
{
    use GlobalSettings;
    private $statsUrl;
    private $responses;

    public function __construct()
    {
        $this->responses = [];
    }

    public function handleRequest(RequestInterface $request): RequestInterface
    {
        $key = $this->genKey($this->convertUri($request->getUri()));

        if (Cache::has($key)) {
            $tag = Cache::get($key);
            $request = $request->withAddedHeader('if-none-match', $tag);
        }

        return $request;
    }

    public function handleTransferStats(TransferStats $stats)
    {
        if ($stats->hasResponse()) {
            $url = $this->convertUri($stats->getEffectiveUri());
            if ($stats->getResponse()->hasHeader('X-Github-Request-Id')) {
                $this->responses[$stats->getResponse()->getHeaderLine('X-Github-Request-Id')] = $url;
            }
            $this->statsUrl = $url;
        }
    }

    public function handleResponse(ResponseInterface $response): ResponseInterface
    {
        if ($response->hasHeader('etag')) {
            $etag = $response->getHeaderLine('etag');
            $status = $response->getStatusCode();

            if ($response->hasHeader('X-Github-Request-Id')) {
                $ghid = $response->getHeaderLine('X-Github-Request-Id');
                $url = array_key_exists($ghid, $this->responses) ? $this->responses[$ghid] :  $this->statsUrl;
                unset($this->responses[$ghid]);
            } else {
                $url = $this->statsUrl;
            }

            $body = trim((string) $response->getBody());

            if ($status === 304 && Cache::has($etag)) {
                $data = Cache::get($etag);
                foreach ($data['headers'] as $name => $values) {
                    $response = $response->withAddedHeader($name, $values);
                }
                $response = $response->withStatus($data['status']);
                $response = $response->withBody(Utils::streamFor($data['body']));
                Log::info("Etag module: Served {$url} via cache!");
            } else if ($status >= 200 && $status < 300 && !empty($body)) {
                $cache = ['body' => $body, 'status' => $status, 'headers' => $response->getHeaders()];
                $response = $response->withBody(Utils::streamFor($body));
                Cache::put($etag, $cache, $this->cache_timeout + 30); // Keep value data 30 seconds longer for request
                Cache::put($this->genKey($url), $etag, $this->cache_timeout);
                Log::info("Etag module: New cache saved!");
            } else if ($status === 304) {
                Log::error("Etag module: Key $etag in cache, but result not. Nothing to serve!");
            }
        }

        $this->statsUrl = null;

        return $response;
    }

    private function convertUri(UriInterface $uri): string
    {
        $query = $uri->getQuery();
        return $uri->getPath() . ($query ? "?$query" : '');
    }
    private function genKey(string $url)
    {
        return "{$this->apiKey}-{$url}";
    }
}
