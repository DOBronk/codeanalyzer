<?php

namespace App\Services\GitHubApi;

use InvalidArgumentException;

final class Settings
{
    private static $instance;
    private $key;
    private $url;
    public readonly int $cache_timeout;
    public readonly int $per_page;

    private function __construct()
    {
        $this->per_page = config('services.github.per_page');
        $this->cache_timeout = config('services.github.etag_timeout', 3600 * 24  * 7);
        $this->url = 'https://api.github.com';
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
            if (!filter_var($value, FILTER_VALIDATE_URL)) {
                throw new InvalidArgumentException("String value is not a valid URL");
            }
            $this->url = $value;
        }
    }
}
