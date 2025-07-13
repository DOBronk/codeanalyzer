<?php

namespace App\Services;

use App\Models\Jobs;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Exceptions\GithubExceptions\ConflictException;
use App\Exceptions\GithubExceptions\ValidationException;
use App\Exceptions\GithubExceptions\ResourceNotFoundException;

use function PHPUnit\Framework\isArray;

class GithubService
{
    public function __construct(private readonly string $uri, private string $key) {}

    /**
     * Function to return an exception based on a HTTP status code
     * @param int $status HTTP Status Code
     */
    private function handleStatus(int $status): \Exception
    {
        return match ($status) {
            401 => new \Exception('Login failed, please check your API key settings'),
            404 => new ResourceNotFoundException('Resource not found!'),
            409 => new ConflictException('Conflict!'),
            410 => new \Exception('Issues are disabled for this repository'),
            422 => new ValidationException('Validation error!'),
            default => new \Exception('Unkown status code')
        };
    }
    /**
     * Recursively retrieve all files ending with .php from provided git tree
     * @param array|string Owner of repository or can be an associative array with all values
     * @param string $repository Repository name
     * @param string $sha Branch name or SHA for branch
     * @return array Returns an array with filepath as key and corresponding SHA
     */
    public function getPhpFilesFromTree(array|string $value, ?string $repository = null, ?string $branch = 'main'): array
    {
        if (!is_array($value)) {
            $owner = $value;
        } elseif (count(array_intersect(array_keys($value), ['owner', 'repository', 'branch'])) === 3) {
            [$owner, $repository,  $branch] = [$value['owner'], $value['repository'], $value['branch']];
        } else {
            throw new \Exception('Invalid value passed to function');
        }

        $uri = "{$this->uri}/repos/{$owner}/{$repository}/git/trees/{$branch}";
        $http = Http::withToken($this->key)->get($uri, ['recursive' => 1]);

        if ($http->status() !== 200) {
            throw $this->handleStatus($http->status());
        }

        return array_filter($http->json()['tree'], fn($item) => $item['type'] === "blob" && str_ends_with($item['path'], '.php'));
    }

    /**
     * Retrieve blob as a string from git repository
     * @param string $owner Repository owner
     * @param string $repo Repository
     * @param string $sha SHA code for blob
     * @return string|null Return blob as string or null on failure
     */
    public function getBlob(string $owner, string $repository, string $sha, string $api = ''): string|null
    {
        if ($api != '') {
            $this->key = $api;
        }

        $uri = "{$this->uri}/repos/{$owner}/{$repository}/git/blobs/{$sha}";
        $http =  Http::withToken($this->key)->get($uri);
        Log::info("Blob: {$sha} http: {$http}");

        $json = $http->json();
        if ($http->status() == 200 && array_key_exists('content', $json)) {
            return base64_decode($json['content']);
        } else {
            throw $this->handleStatus($http->status());
        }
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
        dump($http);
        dd($http->status());
        if ($http->status() !== 201) {
            throw $this->handleStatus($http->status());
        } else {
            $response = $http->json();
            Log::info("Issue created");
            if (array_key_exists('html_url', $response)) {
                return $response['html_url'];
            } else {
                Log::info("No URL on issue created!", $response);
                return '';
            }
        }
    }
}
