<?php

namespace App\Services\GitHubApi\Http;

use App\Services\GitHubApi\Abstracts\HttpModule;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use App\Services\GitHubApi\Traits\GlobalSettings;
// Laravel HTTP Client Handler, in toekomst misschien interface met adapter design pattern 
// toevoegen om composer package van te maken die op andere HTTP clients kan werken)
class HttpClient
{
    use GlobalSettings;

    private $remember = [];
    private $exclude;

    private $middleware = ['Request' => [], 'Response' => [], 'Final' => []];
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
        if (isset($query) && is_array($query)) {
            $q = '';
            foreach ($query as $key => $value) {
                $q .= "$key=$value&";
            }
            $q = rtrim($q, '&');
        } else {
            $q = $query ?? '';
        }

        $q = empty($q) ?: "?{$q}";

        $response = $this->prepareClient("$uri$q")->get($uri, $query);

        return $this->callFinalHandlers($response);
    }
    public function post(string $uri, array $data = [])
    {
        $response = $this->prepareClient($uri)->post($uri, $data);

        return $this->callFinalHandlers($response);
    }
    public function addModule(HttpModule $mod)
    {
        foreach (array_keys($this->middleware) as $mw) {
            if (is_a($mod, "App\Services\GitHubApi\Contracts\Handles{$mw}")) {
                $this->middleware[$mw][$mod->className] = [$mod, "handle{$mw}"];
            }
        }
    }
    public function deleteModule(string|HttpModule $mod)
    {
        $name = ($mod instanceof HttpModule) ? $mod->className : $mod;
        foreach (array_keys($this->middleware) as $key) {
            if (key_exists($name, $this->middleware[$key])) {
                unset($this->middleware[$key][$name]);
            }
        }
    }
    private function callFinalHandlers(Response $response)
    {
        $mw = 'Final';

        foreach ($this->middleware[$mw] as $cb) {
            $response = $cb($response);
        }

        if (!empty($this->remember)) {
            foreach ($this->remember as $mw => $value) {
                $this->middleware[$mw][$this->exclude] = $value;
            }
            $this->remember = [];
        }

        return $response;
    }
    private function callAllMiddlewhere(RequestInterface|ResponseInterface $param, ?string $url)
    {
        $mw = $param instanceof RequestInterface ? 'Request' : 'Response';

        foreach ($this->middleware[$mw] as $cb) {
            $param = $cb($param, $url);
        }

        return $param;
    }
    public function prepareClient(string $url, string|bool $exclude = false): PendingRequest
    {
        if ($exclude === true) {
            return  Http::withToken($this->apiKey)->baseUrl($this->baseUrl);
        } else if (!empty($exclude) && $exclude !== false) {
            $this->exclude = $exclude;
            foreach (array_keys($this->middleware) as $mw) {
                if (key_exists($exclude, $this->middleware[$mw])) {
                    $this->remember[$mw] = $this->middleware[$mw][$exclude];
                    unset($this->middleware[$mw][$exclude]);
                }
            }
        }

        $client = Http::withToken($this->apiKey)
            ->withRequestMiddleware(fn(RequestInterface $param) => $this->callAllMiddlewhere($param, $url))
            ->withResponseMiddleware(fn(ResponseInterface $param) => $this->callAllMiddlewhere($param, $url))
            ->baseUrl($this->baseUrl);

        return $client;
    }
}
