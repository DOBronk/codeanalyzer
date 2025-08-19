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
    private const CACHE_EXPIRE = (3600 * 24 * 7); // Keep cache for 1 week

    public function handleRequest(RequestInterface $request, $url)
    {
        $key = $this->genKey($url);

        if (Cache::has($key)) {
            $tag = Cache::get($key);
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
                $response = $response->withBody($stream);
                Log::info("Served {$url} via cache!");
            } else if ($status >= 200 and $status < 300) {
                $cached = $response->getBody()->getContents();
                if (Cache::has($this->genKey($url))) {
                    Cache::forget(Cache::get($this->genKey($url)));
                }
                Cache::put($etag, $cached, self::CACHE_EXPIRE + 30); // Keep value data 30 seconds longer for request
                Cache::put($this->genKey($url), $etag, self::CACHE_EXPIRE);
            }
        }

        return $response;
    }

    private function genKey(string $url)
    {
        return "{$this->apiKey}::{$url}";
    }
}
