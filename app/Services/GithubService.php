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
    public function __construct(private readonly string $uri, private readonly string $key)
    {
    }

    /**
     * Recursively retrieve all files ending with .php from provided git tree
     * @param string $owner Repository owner
     * @param string $repo Repository
     * @param string $sha Optional: SHA code for tree (defaults to main)
     * @return array|null Returns an associative array with SHA codes to all blobs or returns null on failure
     */
    public function getPhpFilesFromTree(string $owner, string $repo, string $sha = "main", string $apikey): array|null
    {
        $uri = "{$this->uri}/repos/{$owner}/{$repo}/git/trees/{$sha}";
        $http = $this->httpClient($apikey)->get($uri, ['recursive' => 1]);

        switch ($http->status()) {
            case 200:
                $response = $http->json()['tree'];
                return array_filter($response, function ($item) {
                    return $item['type'] === "blob" && str_ends_with($item['path'], '.php');
                });
            case 401:
                    throw new \Exception('Login failed, please check your API key settings');
            case 404:
                throw new ResourceNotFoundException('Resource not found!');
            case 409:
                throw new ConflictException('Conflict!');
            case 422:
                throw new ValidationException('Validation error!');
            default:
                throw new \Exception('Unkown status code');
        }
    }
    /**
     * Retrieve blob as a string from git repository
     * @param string $owner Repository owner
     * @param string $repo Repository
     * @param string $sha SHA code for blob
     * @return string|null Return blob as string or null on failure
     */
    public function getBlob(string $owner, string $repo, string $sha, string $apikey): string|null
    {
        $uri = "{$this->uri}/repos/{$owner}/{$repo}/git/blobs/{$sha}";
        $http = $this->httpClient($apikey)->get($uri);
        Log::info("Blob: {$sha} http: {$http}");

        $json = $http->json();
        if ($http->status() == 200 && array_key_exists('content', $json)) {
            return base64_decode($json['content']);
        } else {
            return false;
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
    public function createIssue(string $owner, string $repo, string $title, string $body, string $apikey): string
    {
        $uri = "{$this->uri}/repos/{$owner}/{$repo}/issues";
        $http = $this->httpClient($apikey)->post($uri, [
            'title' => $title,
            'body' => $body,
            'labels' => ['AI Generated Issue']
        ]);
        switch ($http->status()) {
            case 201:
                $response = $http->json();
                if(array_key_exists('html_url', $response)) {
                    return $response['html_url'];
                }else{
                    return '';
                }
            case 401:
                throw new \Exception('Login failed, please check your API key settings');
            case 410:
                throw new \Exception('Issues are disabled for this repository');
            default:
                throw new \Exception('Unknown status code: ' . $http->status());
        }
    }


    private function httpClient(string $apikey): PendingRequest
    {
        return Http::withHeaders(["Authorization" => "Bearer " . $apikey]); 
        //  return Http::withHeaders(["Authorization" => "Bearer {$this->key}"]);
    }
}
