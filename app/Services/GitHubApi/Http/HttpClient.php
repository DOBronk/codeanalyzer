<?php

namespace App\Services\GitHubApi\Http;

use App\Services\GitHubApi\Abstracts\HttpModule;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use App\Services\GitHubApi\Traits\GlobalSettings;
use App\Services\GitHubApi\Traits\ShortNames;
use GuzzleHttp\TransferStats;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class HttpClient
{
    use GlobalSettings, ShortNames;
    private $modules = [];
    private $exclude;
    private $lastRoute;
    private $middleware = ['Request' => [], 'Response' => [], 'TransferStats' => [], 'Final' => []];

    /**
     * Laravel HTTP Client handler
     * @param array<HttpModule>|HttpModule $module   Load modules (optional) 
     */
    public function __construct(array|HttpModule|null $modules = null)
    {
        if (isset($modules)) {
            $this->addModules(is_array($modules) ? $modules : [$modules]);
        }
        Http::globalOptions(['on_stats' => function (TransferStats $stats) {
            Log::info("GithubHttpClient: Stats accessed, has it got response: " . $stats->hasResponse() ? "yes" : "no");
            $this->callMiddlewhere($stats);
        }]);
        Http::globalRequestMiddleware(fn(RequestInterface $param) => $this->callMiddlewhere($param));
        Http::globalResponseMiddleware(fn(ResponseInterface $param) => $this->callMiddlewhere($param));
    }

    /**
     * HTTP Get 
     * @param string                Url string
     * @param array|string $query   Array or string (optional) 
     */
    public function get(string $uri, mixed $query = null): Response
    {
        $response = $this->prepareClient()->get($uri, $query);

        return $this->callMiddlewhere($response);
    }
    /**
     * HTTP post 
     * @param string                Url string
     * @param array $data           Array with data (optional) 
     */
    public function post(string $uri, array $data = []): Response
    {
        $response = $this->prepareClient()->post($uri, $data);

        return $this->callMiddlewhere($response);
    }
    /**
     * Add a HttpModule to the HTTP client handler
     * @param HttpModule $mod   Instance of HttpModule
     * @throws \ErrorException  Will be thrown if HttpModule has no implementations for handlers
     */
    public function addModule(HttpModule $mod)
    {
        $loaded = false;

        foreach (array_keys($this->middleware) as $mw) {
            if (is_a($mod, "App\Services\GitHubApi\Contracts\Handles{$mw}")) {
                Log::info("HttpClient: Added {$mod->className} to $mw middlewhere");
                $this->middleware[$mw][$mod->className] = [$mod, "handle{$mw}"];
                $loaded = true;
            }
        }

        if (!$loaded) {
            throw new \ErrorException("Module {$mod->className} cannot be loaded, no matching handler implementations found");
        }

        $this->modules[] = $mod->className;
    }
    /**
     * Add an array of modules to the HTTP client handler 
     * @param array<HttpModule> $modules    Array with HttpModule instances
     * @throws \InvalidArgumentException    Will be thrown when array contains a different object or type
     */
    public function addModules(array $modules)
    {
        foreach ($modules as $module) {
            if ($module instanceof HttpModule) {
                $this->addModule($module);
            } else {
                $type = is_object($module) ? get_class($module) : gettype($module);
                throw new InvalidArgumentException("Only HttpModule as array values are accepted, $type given");
            }
        }
    }
    /**
     * Remove a HttpModule from the HTTP client handler
     * @param HttpModule|string $mod    HttpModule instance or short name of the class (case sensitive)  
     * @return bool                     Returns false of module could not be deleted
     */
    public function deleteModule(string|HttpModule $mod): bool
    {
        $name = ($mod instanceof HttpModule) ? $mod->className : $mod;

        if (!$this->isLoaded($name)) {
            return false;
        }

        foreach (array_keys($this->middleware) as $key) {
            unset($this->middleware[$key][$name]);
        }

        $this->modules = array_filter($this->modules, fn($item) => $item !== $name);

        return true;
    }
    /**
     * Returns if the given module or module name is loaded
     * @param string|HttpModule $module     Module name (case sensitive) or module instance
     * @return bool                         
     */
    public function isLoaded(string|HttpModule $module)
    {
        $name = is_string($module) ? $module : $module->className;
        return !empty(array_column($this->middleware, $name));
    }
    /**
     * Prepares a laravel HTTP client PendingRequest with authentication and options configured
     * @param array<string>|string|bool $exclude    Set true to exclude all modules, or provide the name or array of names to exclude specific modules
     */
    public function prepareClient(array|string|bool $exclude = false): PendingRequest
    {
        $this->exclude = [];

        if ($exclude === true) {
            $this->exclude = $this->modules;
        } else if (!empty($exclude) && $exclude !== false) {
            $this->excludeModule($exclude);
        }

        return Http::github()->withToken($this->apiKey);
    }

    private function callMiddlewhere(mixed $param): mixed
    {
        $mw = $this->mapMiddlewhere($param);

        foreach ($this->middleware[$mw] as $name => $cb) {
            if (!in_array($name, $this->exclude)) {
                $param = $cb($param, $this->lastRoute);
            }
        }

        return $param;
    }
    private function mapMiddlewhere(mixed $param): string
    {
        if ($param instanceof RequestInterface) {

            $query = $param->getUri()->getQuery();
            $path = $param->getUri()->getPath();
            $this->lastRoute = empty($query) ? $path : "$path?$query";
            return 'Request';
        } else if ($param instanceof ResponseInterface) {
            return 'Response';
        } else {
            $interface = $this->shortClassName($param);
            $search = array_keys($this->middleware);
            unset($search[0], $search[1]); // Remove the default middleware from the search
            Log::info("HttpClient: Mapping middlewhere array: " . print_r($search, true));
            foreach ($search as $mw) {
                if (str_contains($interface, $mw)) {
                    return $mw;
                }
            }
        }

        return 'Final';
    }
    // Temporarely exclude module until next request
    private function excludeModule(array|string $modules)
    {
        $mods = is_array($modules) ? $modules : [$modules];

        foreach ($mods as $module) {
            if (!$this->isLoaded($module)) {
                throw new \ErrorException("$module not found in loaded modules");
            }
        }

        $this->exclude = $mods;
    }
}
