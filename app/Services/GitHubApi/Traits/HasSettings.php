<?php

namespace App\Services\GitHubApi\Traits;

trait HasSettings
{
    private $key;
    private $url;
    protected $settingsChanged;

    public string $apiKey {
        get => $this->key;
        set => $this->setValue($value);
    }
    public string $baseUrl {
        get => $this->url;
        set => $this->setValue($value);
    }
    private function setValue(string $value)
    {
        $this->settingsChanged = true;
        return $value;
    }
}
