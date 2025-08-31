<?php

namespace App\Services\GitHubApi\Traits;

use App\Services\GitHubApi\Settings;

trait GlobalSettings
{
    public string $apiKey {
        get => Settings::getInstance()->apiKey;
        set {
            Settings::getInstance()->apiKey = $value;
        }
    }

    public int $cache_timeout {
        get => Settings::getInstance()->cache_timeout;
    }

    public int $per_page {
        get => Settings::getInstance()->per_page;
    }

    public string $baseUrl {
        get => Settings::getInstance()->baseUrl;
        set {
            Settings::getInstance()->baseUrl = $value;
        }
    }
}
