<?php

namespace App\Services\GitHubApi\GitHubDatabase;

use App\Abstracts\ApiController;
use App\Traits\GitRepository;

class Blobs extends ApiController
{
    use GitRepository;
    /**
     * Retrieve blob as a string from git repository
     *
     * @param  string  $sha  SHA code for blob
     * @return string|null Return blob as string or null on failure
     */
    public function getBlob(string $sha): string
    {
        // if ($this->isEmpty()) {
        //     throw new \Exception(trans('messages.missingrepository'));
        //  }

        $response = $this->get("/repos/{$this->owner}/{$this->repository}/git/blobs/{$sha}");

        return base64_decode($response['content']);
    }
}
