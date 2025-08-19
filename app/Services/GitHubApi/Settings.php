<?php

namespace App\Services\GitHubApi;

final class Settings
{
    private static $instance;
    private $key;
    private $url;
    private function __construct()
    {
        $key = '';
        $url = 'https://api.github.com';
    }
    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public string $apiKey {
        get => $this->key;
        set {
            $this->key = $value;
        }
    }

    public string $baseUrl {
        get => $this->url;
        set {
            $this->url = $value;
        }
    }
}
