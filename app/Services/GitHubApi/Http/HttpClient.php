<?php

namespace App\Services\GitHubApi\Http;

use App\Services\GitHubApi\Contracts\HttpModuleInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use App\Services\GitHubApi\Traits\HasSettings;

// Laravel HTTP Client Handler, in toekomst misschien interface met adapter design pattern 
// toevoegen om composer package van te maken die op andere HTTP clients kan werken)
class HttpClient
{
    use HasSettings;

    // Voor nu puur implementatie GuzzleHttp van Laravel
    private $middleware = ['request' => [], 'response' => []];

    public function __construct(string $url, string $key)
    {
        $this->key = $key;
        $this->url = $url;
    }
    public function get(string $uri, array|string|null $query = null)
    {
        Log::info("Get url", ['url' => "{$this->url}{$uri}"]);

        return $this->prepareClient($uri)->get("{$uri}", $query);
    }
    public function post(string $uri, array $data = [])
    {
        Log::info("Posted data", ['url' => "{$this->url}{$uri}"]);

        return $this->prepareClient($uri)->post("{$uri}", $data);
    }
    public function addModule(HttpModuleInterface $mod)
    {
        $this->addRequestHandler($mod->getRequestHandler(), get_class($mod));
        $this->addResponseHandler($mod->getResponseHandler(), get_class($mod));
    }

    public function deleteModule(string $mod)
    {
        $this->deleteRequestHandler($mod);
        $this->deleteResponseHandler($mod);
    }
    public function addRequestHandler(callable $handler, string $name)
    {
        $this->middleware['request'][$name] = $handler;
    }

    public function addResponseHandler(callable $handler, string $name)
    {
        $this->middleware['response'][$name] = $handler;
    }

    public function deleteRequestHandler(string $name)
    {
        unset($this->middleware['request'][$name]);
    }

    public function deleteResponseHandler(string $name)
    {
        unset($this->middleware['response'][$name]);
    }

    private function callAllMiddlewhere(RequestInterface|ResponseInterface $param, ?string $url)
    {
        $mw = $param instanceof RequestInterface ? 'request' : 'response';

        foreach ($this->middleware[$mw] as $cb) {
            $param = $cb($param, $url);
        }

        return $param;
    }
    private function prepareClient(string $url): PendingRequest
    {
        return Http::withToken($this->key)
            ->withRequestMiddleware(function (RequestInterface $param) use ($url) {
                return $this->callAllMiddlewhere($param, $url);
            })
            ->withResponseMiddleware(function (ResponseInterface $param) use ($url) {
                return $this->callAllMiddlewhere($param, $url);
            })->baseUrl($this->url);
    }
}
