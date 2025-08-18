<?php

namespace App\Services\GitHubApi;

use App\Services\GitHubApi\Abstracts\ApiController;
use App\Services\GitHubApi\GitData\Blobs;
use App\Services\GitHubApi\GitData\Trees;

class GitData extends ApiController
{
    public function blobs(): Blobs
    {
        return new Blobs($this->getService());
    }
    public function trees(): Trees
    {
        return new Trees($this->getService());
    }
}
