<?php

namespace App\Services;

use App\Abstracts\ApiController;
use App\Interfaces\IGithubService;
use App\Traits\GitRepository;

class GithubService extends ApiController implements IGithubService
{
    use GitRepository;

    public function __construct(private string $uri, string $key)
    {
        $this->uri .= '/repos/';
        parent::__construct($key);
    }

    public function setApi($api)
    {
        $this->setApiKey($api);
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
        $this->setRepositoryArray($values);

        $response = $this->get("{$this->uri}{$this->owner}/{$this->repository}/git/trees/{$this->branch}", ['recursive' => 1]);

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
    public function getBlob(string $sha): string
    {
        // if ($this->isEmpty()) {
        //     throw new \Exception(trans('messages.missingrepository'));
        //  }

        $response = $this->get("{$this->uri}{$this->owner}/{$this->repository}/git/blobs/{$sha}");

        return base64_decode($response['content']);
    }

    /**
     * Summary of createIssue
     *
     * @return bool
     */
    public function createIssue(string $owner, string $repository, string $title, string $body): string
    {
        $response = $this->post("{$this->uri}{$owner}/{$repository}/issues", [
            'title' => $title,
            'body' => $body,
            'labels' => ['AI Generated Issue'],
        ]);

        return $response['html_url'];
    }
}
