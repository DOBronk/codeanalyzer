<?php

namespace App\Services\GitHubApi\GitData;

use App\Services\GitHubApi\Abstracts\ApiController;
use Illuminate\Support\Facades\Log;

class Blobs extends ApiController
{
    /** Retrieve blob as a string from git repository
     *
     * @param  string  $sha  SHA code for blob
     * @return string|null Return blob as string or null on failure
     */
    public function getBlob(string $sha, string $owner, string $repository): string
    {
        $response = $this->get("/repos/{$owner}/{$repository}/git/blobs/{$sha}");
        Log::info(print_r($response, true));
        return base64_decode($response['content']);
    }
}
