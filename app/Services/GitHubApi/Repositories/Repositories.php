<?php

namespace App\Services\GitHubApi\Repositories;

use App\Services\GitHubApi\Abstracts\ApiController;

class Repositories extends ApiController
{
    public function getRepositories(string $owner)
    {
        $data = $this->get("/users/{$owner}/repos", ['per_page' => 50, 'page' => 1])->json();

        $repositories = array_column($data, 'name');
        $default_branches = array_column($data, 'default_branch');

        return [$repositories, $default_branches];
    }

    public function getRepository(string $owner, string $repository)
    {
        return $this->get("/repos/{$owner}/{$repository}");
    }
}
