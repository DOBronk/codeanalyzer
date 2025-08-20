<?php

namespace App\Services\GitHubApi\Http\Modules;

use App\Services\GitHubApi\Traits\GlobalSettings;
use App\Services\GitHubApi\Abstracts\HttpModule;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Psr7;

class EtagCache extends HttpModule
{
    use GlobalSettings;
    private $cache_expire;

    public function __construct()
    {
        $this->cache_expire = config('services.github.etag_timeout', 3600 * 24  * 7); // Default 1 week (dev mode) ;
    }
    public function handleRequest(RequestInterface $request, $url)
    {
        $key = $this->genKey($url);

        if (Cache::has($key)) {
            $tag = Cache::get($key);
            Log::info("Etag module: Key: $tag @ $url");
            $request = $request->withAddedHeader('if-none-match', $tag);
        }

        return $request;
    }
    public function handleResponse(ResponseInterface $response, string $url)
    {
        if ($response->hasHeader('etag')) {
            $etag = $response->getHeaderLine('etag');
            $status = $response->getStatusCode();

            if ($status === 304 && Cache::has($etag)) {
                $stream = Psr7\Utils::streamFor(Cache::get($etag));
                $response = $response->withBody($stream)
                    ->withStatus(200, 'Served from cache');
                Log::info("Etag module: Served {$url} via cache!");
            } else if ($status >= 200 and $status < 300) {
                $cached = $response->getBody()->getContents();
                if (Cache::has($this->genKey($url))) {
                    Cache::forget(Cache::get($this->genKey($url)));
                }
                Cache::put($etag, $cached, $this->cache_expire + 30); // Keep value data 30 seconds longer for request
                Cache::put($this->genKey($url), $etag, $this->cache_expire);
            } else if ($status === 304) {
                // Something has gone completely wrong, etag is in cache but the response not
                Log::error("Etag module: Key: $etag in cache, but result not. Nothing to serve!");
            }
        }

        return $response;
    }

    private function genKey(string $url)
    {
        return "{$this->apiKey}::{$url}";
    }
}
