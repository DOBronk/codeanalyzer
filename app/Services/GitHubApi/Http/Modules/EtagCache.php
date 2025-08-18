<?php

namespace App\Services\GitHubApi\Http\Modules;

use App\Services\GitHubApi\Traits\HasSettings;
use App\Services\GitHubApi\Abstracts\HttpModule;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Psr7;

class EtagCache extends HttpModule
{
    use HasSettings;
    private const CACHE_EXPIRE = (3600 * 24 * 7); // Keep cache for 1 week

    public function __construct(string $key)
    {
        $this->key = $key;
    }
    public function handleRequest(RequestInterface $request, $url)
    {
        $key = $this->genKey($url);

        if (Cache::has($key)) {
            $tag = Cache::get($key);
            $request = $request->withAddedHeader('if-none-match', $tag);
            Log::info("Etag header found in cache!", ['etag' => $tag]);
        }

        return $request;
    }
    public function handleResponse(ResponseInterface $response, string $url)
    {
        if ($response->hasHeader('etag')) {
            $etag = $response->getHeaderLine('etag');
            $status = $response->getStatusCode();

            if ($status === 304) {
                if (Cache::has($etag)) {
                    $stream = Psr7\Utils::streamFor(Cache::get($etag));
                    $response = $response->withBody($stream);
                    Log::info("Served via local cache!");
                } else {
                    Log::error("Response 304 not modified, but etag value not found in cache", ['etag' => $etag]);
                }
            } else if ($status >= 200 and $status < 300) {
                $cached = $response->getBody()->getContents();
                Cache::add($etag, $cached, self::CACHE_EXPIRE + 30); // Keep value data 30 seconds longer for request
                Cache::add($this->genKey($url), $etag, self::CACHE_EXPIRE);
            }
        }

        return $response;
    }

    private function genKey(string $url)
    {
        return "{$this->key}{$url}";
    }
}
