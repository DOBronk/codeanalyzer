<?php 

namespace App\Services\GitHubApi\Contracts;

interface ApiSettingsInterface
{
    // Get API key
    public function getKey(): string;
    // Set API key
    public function setKey(string $key);
    // Get API Base url
    public function getUrl(): string;
    // Set API Base url
    public function setUrl(string $url);    
}