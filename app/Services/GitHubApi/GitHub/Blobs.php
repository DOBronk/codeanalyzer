<?php

namespace App\Services\GitHubApi\GitHub;

use App\Services\GitHubApi\Abstracts\ApiController;

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

        return base64_decode($response['content']);
    }
    /**
     * Get an array of blobs from an array of SHA keys
     * 
     * @param array<string> $sha_array
     * @param string $owner
     * @param string $repo
     * @return array<string>
     */
    public function getBlobs(array $sha_array, string $owner, string $repo): array
    {
        return array_map(fn($sha) => $this->getBlob($sha, $owner, $repo), $sha_array);
    }
}
