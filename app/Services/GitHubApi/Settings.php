<?php

namespace App\Services\GitHubApi;

use InvalidArgumentException;
use App\Services\GitHubApi\Events\SettingsChanged;

final class Settings
{
    private $key;
    private $url;
    public readonly int $cache_timeout;
    public readonly int $per_page;
    public function __construct(string $url, ?string $key)
    {
        $this->per_page = config('services.github.per_page');
        $this->cache_timeout = config('services.github.etag_timeout', 3600); # Developmental value 60 minutes cache
        $this->url = $url;
        $this->key = $key ?? '';
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
