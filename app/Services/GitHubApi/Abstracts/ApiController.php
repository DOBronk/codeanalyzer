<?php

namespace App\Services\GitHubApi\Abstracts;

use App\Services\GithubService;

abstract class ApiController
{
    public function __construct(private GitHubService $git) {}

    public function getService(): GithubService
    {
        return $this->git ?? null;
    }
    public function config(): self
    {
        return $this;
    }
    public function get(string $uri, array|string|null $query = null)
    {
        return $this->http()->get($uri, $query);
    }
    public function post(string $uri, array $data = [])
    {
        return $this->http()->post($uri, $data);
    }
    private function http()
    {
        return $this->git->getHttpClient();
    }
}
