<?php

namespace App\Services\GitHubApi\GitHub;

use App\Services\GitHubApi\Abstracts\ApiController;

class Repositories extends ApiController
{
    public function getRepositories(string $owner): array
    {
        $data = $this->get("/users/{$owner}/repos");

        $repositories = array_column($data, 'name');
        $default_branches = array_column($data, 'default_branch');

        return [$repositories, $default_branches];
    }

    public function getRepository(string $owner, string $repository): array
    {
        return $this->get("/repos/{$owner}/{$repository}");
    }
}
