<?php

namespace App\Services;

use App\Abstracts\ApiController;

class GithubService extends ApiController
{
    public function __construct(private readonly string $uri, string $key)
    {
        parent::__construct($key);
    }

    /**
     * Recursively retrieve all files ending with .php from provided git tree
     *
     * @param array|string Owner of repository or can be an associative array with all values
     * @param  string  $repository  Repository name
     * @param  string  $sha  Branch name or SHA for branch
     * @return array Returns an array with filepath as key and corresponding SHA
     */
    public function getRepository(array $values, string $extension = '.php'): array
    {
        $this->setRepository($values);

        $response = $this->get("{$this->uri}/repos/{$this->owner}/{$this->repository}/git/trees/{$this->branch}", ['recursive' => 1]);

        return array_filter($response['tree'], fn($item) => $item['type'] === 'blob' && str_ends_with($item['path'], $extension));
    }

    /**
     * Retrieve blob as a string from git repository
     *
     * @param  string  $owner  Repository owner
     * @param  string  $repo  Repository
     * @param  string  $sha  SHA code for blob
     * @return string|null Return blob as string or null on failure
     */
    public function getBlob(string $sha, string|null $owner = null, string|null $repository = null): ?string
    {
        if (isset($owner) && isset($repository)) {
            $this->setRepository($owner, $repository);
        }

        $response = $this->get("{$this->uri}/repos/{$this->owner}/{$this->repository}/git/blobs/{$sha}");

        return base64_decode($response['content']);
    }

    /**
     * Summary of createIssue
     *
     * @return bool
     */
    public function createIssue(string $owner, string $repository, string $title, string $body): string
    {
        $response = $this->post("{$this->uri}/repos/{$owner}/{$repository}/issues", [
            'title' => $title,
            'body' => $body,
            'labels' => ['AI Generated Issue'],
        ]);

        return $response['html_url'];
    }
}
