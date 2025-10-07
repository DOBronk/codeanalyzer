<?php

namespace App\Services\GitHubApi\Http;

use App\Services\GitHubApi\Settings;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use App\Services\GitHubApi\Traits\ShortNames;
use GuzzleHttp\TransferStats;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

class HttpClient
{
    use ShortNames;
    private $client;
    private $params;
    private $stack;
    /**
     * Laravel HTTP Client handler
     * @param \App\Services\GitHubApi\Http\MiddlewareDispatcher $dispatcher
     * @param \App\Services\GitHubApi\Http\ModuleManager $modules
     */
    public function __construct(private MiddlewareDispatcher $dispatcher, private ModuleManager $modules, private Settings $settings)
    {
        $this->params = [];
        $this->stack = HandlerStack::create();
        $this->stack->push(Middleware::mapRequest(fn(RequestInterface $param) => $this->dispatcher->dispatch($param, $this->modules->get(), $this->params)));
        $this->stack->push(Middleware::mapResponse(fn(ResponseInterface $param) => $this->dispatcher->dispatch($param, $this->modules->get(), $this->params)));

        Http::globalOptions(['version' => 2.0, 'on_stats' => function (TransferStats $stats) {
            $this->dispatcher->dispatch($stats, $this->modules->get());
        }]);
        // Make a reusable client
        $this->client = Http::withRequestMiddleware(fn(RequestInterface $param) => $this->dispatcher->dispatch($param, $this->modules->get(), $this->params))
            ->withResponseMiddleware(fn(ResponseInterface $param) => $this->dispatcher->dispatch($param, $this->modules->get(), $this->params))
            ->buildClient();
    }
    /**
     * HTTP Get 
     * @param string                Url string
     * @param array|string $query   Array or string (optional) 
     */
    public function get(string $uri, mixed $query = null, ?array $params = []): Response
    {
        return  $this->prepareClient($params)->get($uri, $query);
    }
    /**
     * HTTP post 
     * @param string                Url string
     * @param array $data           Array with data (optional) 
     */
    public function post(string $uri, array $data = [], ?array $params = []): Response
    {
        return $this->prepareClient($params)->post($uri, $data);
    }
    /**
     * Prepares a laravel HTTP client PendingRequest with authentication and options configured
     * @param array<string>|string|bool $exclude    Set true to exclude all modules, or provide the name or array of names to exclude specific modules
     */
    public function prepareClient(array $params = []): PendingRequest
    {
        $this->params = [$this, ...$params];
        return Http::github()->withHeader('Authorization', "Bearer {$this->settings->apiKey}")->setClient($this->client);
    }

    public function prepareGuzzle(array $headers = []): Client
    {
        $headers = [
            'Accept-Encoding' => 'gzip, deflate',
            'Connection' => 'keep-alive',
            'Accept' => 'application/vnd.github+json',
            'X-GitHub-Api-Version' => '2022-11-28',
            'User-Agent' => 'Codeanalyzer (gh_user: DOBronk)',
            'Authorization' => "Bearer {$this->settings->apiKey}",
            ...$headers
        ];

        return new Client([
            // Connection settings
            'timeout' => 30,              // Total request timeout
            'connect_timeout' => 10,      // Connection establishment timeout
            'read_timeout' => 20,         // Data read timeout

            // HTTP settings
            'http_version' => '2.0',      // Use HTTP/2 for better performance
            'allow_redirects' => [
                'max' => 3,               // Limit redirects
                'strict' => true,
                'referer' => true
            ],

            // SSL settings
            'verify' => true,             // Keep SSL verification enabled

            // Compression
            'decode_content' => true,     // Automatically decode gzipped content

            // Headers
            'headers' => $headers,

            'base_url' => config('codeanalyzer.gh_uri'),

            'on_stats' => function (TransferStats $stats) {
                $this->dispatcher->dispatch($stats, $this->modules->get());
            },

            'handler' =>  $this->stack
        ]);
    }
}
