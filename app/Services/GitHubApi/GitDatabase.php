<?php

namespace App\Services\GitHubApi;

use App\Abstracts\ApiController;
use App\Services\GitHubApi\GitHubDatabase\Blobs;
use App\Services\GitHubApi\GitHubDatabase\Issues;
use App\Services\GitHubApi\GitHubDatabase\Repositories;

class GitDatabase extends ApiController
{
    public function issues(): Issues
    {
        return new Issues($this->getService());
    }
    public function repositories(): Repositories
    {
        return new Repositories($this->getService());
    }
    public function blobs(): Blobs
    {
        return new Blobs($this->getService());
    }
}
