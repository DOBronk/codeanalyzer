<?php

namespace App\Services\GitHubApi\Branches;

use App\Services\GitHubApi\Abstracts\ApiController;

class Branches extends ApiController
{
    public function getBranches(string $owner, string $repository): array
    {
        return array_column($this->get("/repos/{$owner}/{$repository}/branches")->json(), 'name');
    }
}
