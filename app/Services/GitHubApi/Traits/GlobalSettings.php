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

    public string $baseUrl {
        get => Settings::getInstance()->baseUrl;
        set {
            Settings::getInstance()->baseUrl = $value;
        }
    }
}
