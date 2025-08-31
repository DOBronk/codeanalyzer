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
// TODO: Verwijder handlesresponse
class EtagCache extends HttpModule implements HandlesRequest, HandlesTransferStats, HandlesResponse
{
    use GlobalSettings;
    private $statsUrl;

    public function __construct() {}
    public function handleRequest(RequestInterface $request, string $url): RequestInterface
    {
        $key = $this->genKey($url);
        if (Cache::has($key)) {
            $tag = Cache::get($key);
            $request = $request->withAddedHeader('if-none-match', $tag);
        }

        return $request;
    }

    public function handleTransferStats(TransferStats $stats, string $url)
    {
        if ($stats->hasResponse()) {
            [$path, $query] = [$stats->getEffectiveUri()->getPath(), $stats->getEffectiveUri()->getQuery()];
            $this->statsUrl = empty($query) ? $path : "$path?$query";
        }
    }

    public function handleResponse(ResponseInterface $response, string $url): ResponseInterface
    {
        $url = empty($this->statsUrl) ? $url : $this->statsUrl;

        if ($response->hasHeader('etag')) {
            $etag = $response->getHeaderLine('etag');
            $status = $response->getStatusCode();
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

    private function genKey(string $url)
    {
        return "{$this->apiKey}+-{$url}";
    }
}
