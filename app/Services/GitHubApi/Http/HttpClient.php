<?php

namespace App\Services\GitHubApi\Http;

use App\Services\GitHubApi\Contracts\HttpModuleInterface;
use App\Services\GitHubApi\Abstracts\HttpModule;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use App\Services\GitHubApi\Traits\GlobalSettings;

// Laravel HTTP Client Handler, in toekomst misschien interface met adapter design pattern 
// toevoegen om composer package van te maken die op andere HTTP clients kan werken)
class HttpClient
{
    use GlobalSettings;

    private $middleware = ['request' => [], 'response' => []];

    public function __construct(array|HttpModule|null $modules = null)
    {
        if (isset($modules)) {
            if (is_array($modules)) {
                foreach ($modules as $module) {
                    $this->addModule($module);
                }
            } else {
                $this->addModule($modules);
            }
        }
    }
    public function get(string $uri, array|string|null $query = null)
    {
        return $this->prepareClient($uri)->get("{$uri}", $query);
    }
    public function post(string $uri, array $data = [])
    {
        return $this->prepareClient($uri)->post("{$uri}", $data);
    }
    public function addModule(HttpModule $mod)
    {
        $this->middleware = array_merge($this->middleware, $mod->getHandlers());
    }

    public function deleteModule(string|HttpModule $mod)
    {
        $name = ($mod instanceof HttpModule) ? $mod->className : $mod;
        unset($this->middleware['response'][$name], $this->middleware['request'][$name]);
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
        return Http::withToken($this->apiKey)
            ->withRequestMiddleware(function (RequestInterface $param) use ($url) {
                return $this->callAllMiddlewhere($param, $url);
            })
            ->withResponseMiddleware(function (ResponseInterface $param) use ($url) {
                return $this->callAllMiddlewhere($param, $url);
            })->baseUrl($this->baseUrl);
    }
}
