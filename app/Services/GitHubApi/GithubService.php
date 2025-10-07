<?php

namespace App\Services\GitHubApi;

use App\Services\GitHubApi\Abstracts\ApiController;
use App\Services\GitHubApi\Branches\Branches;
use App\Services\GitHubApi\GitData;
use App\Services\GitHubApi\Http\HttpClient;
use App\Services\GitHubApi\Http\Modules\Paginator;
use App\Services\GitHubApi\Issues\Issues;
use App\Services\GitHubApi\Repositories\Repositories;
use App\Services\GitHubApi\Traits\GlobalSettings;
use App\Services\GitHubApi\Http\Builder;

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
    private $httpClient;
    private Settings $settings;
    public function __construct(string $url, ?string $key)
    {
        $this->settings = new Settings($url, $key);
        $this->httpClient = new Builder($this->settings);
    }
    /**
     * Returns the instance of itself
     * @return GithubService
     */
    public function getService(): self
    {
        return $this;
    }

    public function config(): Settings
    {
        return $this->settings;
    }
    public function getHttp(): Builder
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
