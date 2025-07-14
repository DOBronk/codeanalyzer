<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Exceptions\GithubExceptions\ConflictException;
use App\Exceptions\GithubExceptions\ValidationException;
use App\Exceptions\GithubExceptions\ResourceNotFoundException;

use App\Services\EnumStatus as Status;

class GithubService
{
    private const REPO = ['owner', 'repository', 'branch'];

    public function __construct(private readonly string $uri, private string $key) {}

    /**
     * Function to return an exception based on a HTTP status code
     * @param int $status HTTP Status Code
     */
    private function handleStatus(Response $http): Exception|array|null
    {
        return match (Status::from($http->status())) {
            Status::OK => $http->json(),
            Status::Created => $http->json(),
            Status::NoContent => null,
            Status::AuthFailed => new Exception('Login failed, please check your API key settings'),
            Status::ResourceNotFound => new ResourceNotFoundException('Resource not found!'),
            Status::Conflict => new ConflictException('Conflict!'),
            Status::IssuesDisable => new Exception('Issues are disabled for this repository'),
            Status::ValidationError => new ValidationException('Validation error!'),
            default => new Exception('Unkown status code')
        };
    }
    /**
     * Recursively retrieve all files ending with .php from provided git tree
     * @param array|string Owner of repository or can be an associative array with all values
     * @param string $repository Repository name
     * @param string $sha Branch name or SHA for branch
     * @return array Returns an array with filepath as key and corresponding SHA
     */
    public function getRepository(array $values, string $extension = ".php"): array
    {
        if (count(array_intersect(array_keys($values), self::REPO)) === 3) {
            [$owner, $repository,  $branch] = [$values['owner'], $values['repository'], $values['branch']];
        } else {
            throw new Exception('Invalid value passed to function');
        }

        $uri = "{$this->uri}/repos/{$owner}/{$repository}/git/trees/{$branch}";
        $http = Http::withToken($this->key)->get($uri, ['recursive' => 1]);

        if (($response = $this->handleStatus($http)) instanceof Exception) {
            throw $response;
        }

        return array_filter($response['tree'], fn($item) => $item['type'] === "blob" && str_ends_with($item['path'], $extension));
    }

    /**
     * Retrieve blob as a string from git repository
     * @param string $owner Repository owner
     * @param string $repo Repository
     * @param string $sha SHA code for blob
     * @return string|null Return blob as string or null on failure
     */
    public function getBlob(string $owner, string $repository, string $sha, ?string $api = null): ?string
    {
        $uri = "{$this->uri}/repos/{$owner}/{$repository}/git/blobs/{$sha}";
        $http =  Http::withToken($api ?? $this->key)->get($uri);

        if (($response = $this->handleStatus($http)) instanceof Exception) {
            throw $response;
        }

        return base64_decode($response['content']);
    }
    /**
     * Summary of createIssue
     * @param string $owner
     * @param string $repository
     * @param string $title
     * @param string $body
     * @return bool
     */
    public function createIssue(string $owner, string $repository, string $title, string $body): string
    {
        $uri = "{$this->uri}/repos/{$owner}/{$repository}/issues";

        $http = Http::withToken($this->key)->post($uri, [
            'title' => $title,
            'body' => $body,
            'labels' => ['AI Generated Issue']
        ]);

        if (($response = $this->handleStatus($http)) instanceof Exception) {
            throw $response;
        }

        return $response['html_url'];
    }
}
