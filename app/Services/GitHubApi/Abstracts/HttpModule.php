<?php

namespace App\Services\GitHubApi\Abstracts;

use App\Services\GitHubApi\Contracts\HttpModuleInterface;
use App\Services\GitHubApi\Traits\ShortNames;

abstract class HttpModule
{
    use ShortNames;

    public string $className {
        get {
            return $this->shortClassName($this);
        }
    }
}
