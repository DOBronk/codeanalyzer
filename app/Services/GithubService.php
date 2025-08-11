<?php

namespace App\Services;

use App\Abstracts\ApiController;
use App\Services\GitHubApi\GitDatabase;
use App\Services\GitHubApi\HttpClient;
/**
 * Service die met GitHub REST API communiceert
 * 
 * @method App\Services\GithubApi\GitDatabase     gitDatabase()
 * 
 */
class GithubService
{
    private $httpClient;
    public function __construct(string $url, private string $key)
    {
        $this->httpClient = new HttpClient($this, $url);
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getHttpClient(): HttpClient
    {
        return $this->httpClient;
    }
    public function github($name): ?ApiController
    {
        if ($name === 'gitDatabase' || $name === 'gitDb') {
            return new GitDatabase($this);
        }
        return null;
    }

    public function gitData(): GitDatabase
    {
        return new GitDatabase($this);
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
