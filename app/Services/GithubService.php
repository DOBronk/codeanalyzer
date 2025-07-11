<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Exceptions\GithubExceptions\ConflictException;
use App\Exceptions\GithubExceptions\ValidationException;
use App\Exceptions\GithubExceptions\ResourceNotFoundException;

class GithubService
{
    public function __construct(private readonly string $uri, private string $key) {}

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
     * @param string $owner Repository owner
     * @param string $repo Repository
     * @param string $sha Optional: SHA code for tree (defaults to main)
     * @return array|null Returns an associative array with SHA codes to all blobs or returns null on failure
     */
    public function getPhpFilesFromTree(string $owner, string $repo, string $sha = "main"): array|null
    {
        $uri = "{$this->uri}/repos/{$owner}/{$repo}/git/trees/{$sha}";
        $http = Http::withToken($this->key)->get($uri, ['recursive' => 1]);

        if ($http->status() !== 200) {
            throw $this->handleStatus($http->status());
        } else {
            return array_filter($http->json()['tree'], fn($item) => $item['type'] === "blob" && str_ends_with($item['path'], '.php'));
        }
    }
    /**
     * Retrieve blob as a string from git repository
     * @param string $owner Repository owner
     * @param string $repo Repository
     * @param string $sha SHA code for blob
     * @return string|null Return blob as string or null on failure
     */
    public function getBlob(string $owner, string $repo, string $sha, string $api = ''): string|null
    {
        if ($api != '') {
            $this->key = $api;
        }

        $uri = "{$this->uri}/repos/{$owner}/{$repo}/git/blobs/{$sha}";
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
     * @param string $repo
     * @param string $title
     * @param string $body
     * @return bool
     */
    public function createIssue(string $owner, string $repo, string $title, string $body): string
    {
        $uri = "{$this->uri}/repos/{$owner}/{$repo}/issues";
        $http = Http::withToken($this->key)->post($uri, [
            'title' => $title,
            'body' => $body,
            'labels' => ['AI Generated Issue']
        ]);
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
