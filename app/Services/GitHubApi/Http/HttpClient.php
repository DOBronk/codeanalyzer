<?php

namespace App\Services\GitHubApi\Http;

use App\Services\GitHubApi\Contracts\HttpModuleInterface;
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

    // Voor nu puur implementatie GuzzleHttp van Laravel
    private $middleware = ['request' => [], 'response' => []];

    public function __construct(array|HttpModuleInterface|null $modules = null)
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
        Log::info("Get url", ['url' => "{$this->baseUrl}{$uri}"]);

        return $this->prepareClient($uri)->get("{$uri}", $query);
    }
    public function post(string $uri, array $data = [])
    {
        Log::info("Posted data", ['url' => "{$this->baseUrl}{$uri}"]);

        return $this->prepareClient($uri)->post("{$uri}", $data);
    }
    public function addModule(HttpModuleInterface $mod)
    {
        $name = get_class($mod);
        $this->middleware['request'][$name] = $mod->getRequestHandler();
        $this->middleware['response'][$name] = $mod->getResponseHandler();
    }

    public function deleteModule(string|HttpModuleInterface $mod)
    {
        $name = ($mod instanceof HttpModuleInterface) ? get_class($mod) : $mod;
        unset($this->middleware['response'][$name]);
        unset($this->middleware['request'][$name]);
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
