<?php

namespace App\Services\GitHubApi\Traits;

use App\Services\GitHubApi\Http\HttpClient;
use App\Services\GitHubApi\Abstracts\HttpModule;

trait HttpClientBuilder
{
    protected $httpClient;
    protected $settingsChanged;
    protected $modules = [];
    protected function setHttpClient()
    {
        if ($this->settingsChanged ?? true) {
            $this->settingsChanged = false;
            $this->httpClient = new HttpClient($this->url, $this->key);
        }
    }
    protected function constructClient(string $key, string $url, ?HttpModule ...$modules)
    {
        $this->key = $key;
        $this->url = $url;

        if (isset($modules)) {
            foreach ($modules as $module) {
                $this->addModule($module);
            }
        }
    }
    public function addModule(HttpModule $module)
    {
        $this->setHttpClient();
        $this->httpClient->addModule($module);
        $this->modules[get_class($module)] = $module;
    }

    public function removeModule(HttpModule $module)
    {
        if (! isset($this->httpClient)) {
            return;
        }
        $this->httpClient->addModule($module);
        $this->modules[get_class($module)] = $module;
    }
    public function getHttpClient(): HttpClient
    {
        $this->setHttpClient();
        return $this->httpClient;
    }
}
