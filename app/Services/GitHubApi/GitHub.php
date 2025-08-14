<?php

namespace App\Services\GitHubApi;

use App\Services\GitHubApi\Abstracts\ApiController;
use App\Services\GitHubApi\GitHub\Blobs;
use App\Services\GitHubApi\GitHub\Issues;
use App\Services\GitHubApi\GitHub\Repositories;
use App\Services\GitHubApi\GitHub\Branches;

class GitHub extends ApiController
{
    public function issues(): Issues
    {
        return new Issues($this->getService());
    }
    public function branches(): Branches
    {
        return new Branches($this->getService());
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
