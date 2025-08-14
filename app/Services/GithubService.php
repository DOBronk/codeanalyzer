<?php

namespace App\Services;

use App\Services\GitHubApi\Abstracts\ApiController;
use App\Services\GitHubApi\GitHub;
use App\Services\GitHubApi\HttpClient;
use App\Services\GitHubApi\Settings;
use Illuminate\Support\Facades\Log;

/**
 * Service die met GitHub REST API communiceert of configureert
 * 
 * @method App\Services\GithubApi\GitHub    git()
 * 
 */
class GithubService
{
    private $httpClient;
    private $settings;

    public function __construct(string $url, private string $key)
    {
        $this->settings = new Settings($key, $url);
        $this->httpClient = new HttpClient($this->settings);
    }
    public function getHttpClient(): HttpClient
    {
        return $this->httpClient;
    }
    public function getService(): self
    {
        return $this;
    }
    public function config(): Settings
    {
        return $this->settings;
    }
    public function github($name): ApiController
    {
        return match ($name) {
            'git' => new GitHub($this),
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
