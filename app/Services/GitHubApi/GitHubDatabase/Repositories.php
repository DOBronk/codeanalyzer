<?php

namespace App\Services\GitHubApi\GitHubDatabase;

use App\Abstracts\ApiController;

class Repositories extends ApiController
{
    /**
     * Recursively retrieve all files ending with .php from provided git tree
     *
     * @param array|string Owner of repository or can be an associative array with all values
     * @param  string  $repository  Repository name
     * @param  string  $sha  Branch name or SHA for branch
     * @return array Returns an array with filepath as key and corresponding SHA
     */
    public function getRepositoryTree(array $array, string $extension = ".php"): array
    {
        [$owner, $repository, $branch] = [$array['owner'], $array['repository'], $array['branch']];

        return $this->get("/repos/{$owner}/{$repository}/git/trees/{$branch}", ['recursive' => 1])['tree'];
    }

    public function getBranches(string $owner, string $repository): array
    {
        return array_column($this->get("/repos/{$owner}/{$repository}/branches"), 'name');
    }

    public function getRepositories(string $owner): array
    {
        return array_column($this->get("/users/{$owner}/repos"), 'name');
    }

    public function getRepository(string $owner, string $repository): array
    {
        return $this->get("/repos/{$owner}/{$repository}");
    }
}
