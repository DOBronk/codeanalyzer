<?php

namespace App\Services\GitHubApi;

use App\Services\GitHubApi\Contracts\ApiSettingsInterface;

class Settings implements ApiSettingsInterface
{
    const API_VERSION = '2022-11-28';
    private $keys = [];
    private $url;

    public function __construct(?string $key = null, ?string $url = null)
    {
        $this->keys[0] = $key;
        $this->url = $url ?? "https://api.github.com";
    }

    public function getKey(): string
    {
        //if (count($this->keys) === 0) return '';
        return $this->keys[0];
    }
    public function setKey(string $key)
    {
        $this->keys[0] = $key;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url)
    {
        $this->url = $url;
    }
}
