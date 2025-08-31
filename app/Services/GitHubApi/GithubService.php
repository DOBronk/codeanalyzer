<?php

namespace App\Services\GitHubApi;

use App\Services\GitHubApi\Abstracts\ApiController;
use App\Services\GitHubApi\Branches\Branches;
use App\Services\GitHubApi\GitData;
use App\Services\GitHubApi\Http\Modules\EtagCache;
use App\Services\GitHubApi\Http\Modules\Paginator;
use App\Services\GitHubApi\Issues\Issues;
use App\Services\GitHubApi\Repositories\Repositories;
use App\Services\GitHubApi\Traits\GlobalSettings;
use App\Services\GitHubApi\Http\HttpClient;

/**
 * Service die met GitHub REST API communiceert of configureert
 * 
 * @method App\Services\GithubApi\GitData                   data()
 * @method App\Services\GithubApi\GitData                   gitdata()
 * @method App\Services\GithubApi\Branches\Branches         branches()
 * @method App\Services\GithubApi\Issues\Issues             issues()
 * @method App\Services\GithubApi\Repositories\Repositories repositories()
 */
class GithubService
{
    use GlobalSettings;
    private $httpClient;
    public function __construct(string $url, string $key)
    {
        $this->apiKey = $key;
        $this->baseUrl = $url;
        $this->httpClient = new httpClient();
        // Important! Load the cache module first
        $this->httpClient->addModule(new EtagCache());
        $this->httpClient->addModule(new Paginator($this->httpClient));
    }
    /**
     * Returns the instance of itself
     * @return GithubService
     */
    public function getService(): self
    {
        return $this;
    }
    /**
     * Returns the laravel HTTP client handler
     * @return HttpClient
     */
    public function getHttpClient(): HttpClient
    {
        return $this->httpClient;
    }
    public function github($name): ?ApiController
    {
        return match ($name) {
            'data', 'gitdata' => new GitData($this),
            'branches', 'branch' => new Branches($this),
            'repositories', 'repos' => new Repositories($this),
            'issues', 'issue' => new Issues($this),
            default => null
        };
    }
    /**
     * @param string $name
     * @param array  $args
     *
     * @return ApiController
     */
    public function __call($name, $args): ApiController
    {
        if (($ret = $this->github($name)) === null) {
            throw new \Exception("Method {$name} not found");
        }

        return $ret;
    }
}
