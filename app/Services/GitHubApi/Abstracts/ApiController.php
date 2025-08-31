<?php

namespace App\Services\GitHubApi\Abstracts;

use Illuminate\Http\Client\Response;
use App\Services\GitHubApi\GithubService;

abstract class ApiController
{
    public function __construct(private GitHubService $git) {}

    protected function getService(): GithubService
    {
        return $this->git;
    }
    protected function get(string $uri, array|string|null $query = null): Response
    {
        return $this->http()->get($uri, $query);
    }
    protected function post(string $uri, array $data = []): Response
    {
        return $this->http()->post($uri, $data);
    }
    private function http()
    {
        return $this->git->getHttpClient();
    }
}
