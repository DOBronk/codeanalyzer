<?php

namespace App\Services\GitHubApi\Http;

use App\Services\GitHubApi\Http\Modules\EtagCache;
use App\Services\GitHubApi\Http\Modules\Filter;
use App\Services\GitHubApi\Http\Modules\Paginator;
use App\Services\GitHubApi\Settings;

class Builder
{
    private $moduleManager;
    private $dispatcher;
    private $client;
    private $etagCache;

    public function __construct(private Settings $settings, ?ModuleManager $moduleManager = null, ?MiddlewareDispatcher $dispatcher = null)
    {
        $this->moduleManager = $moduleManager ?? new ModuleManager();
        $this->dispatcher = $dispatcher ?? new MiddlewareDispatcher();
        $this->etagCache = new EtagCache($this->settings);
        $this->moduleManager->add($this->etagCache, true);
        $this->moduleManager->add(new Paginator());
        $this->moduleManager->add(new Filter());
        $this->client = new HttpClient($this->dispatcher, $this->moduleManager, $this->settings);
    }
    public function getClient(): HttpClient
    {
        return $this->client;
    }
    public function modules(): ModuleManager
    {
        return $this->moduleManager;
    }
}
