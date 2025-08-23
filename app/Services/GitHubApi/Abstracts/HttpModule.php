<?php

namespace App\Services\GitHubApi\Abstracts;

use App\Services\GitHubApi\Contracts\HttpModuleInterface;

abstract class HttpModule
{
    public string $className {
        get {
            $fqclass = get_class($this);
            return substr($fqclass, strrpos($fqclass, '\\') + 1);
        }
    }
}
