<?php

namespace App\Services\GitHubApi\Contracts;

interface HttpModuleInterface
{
    public function getHandlers(): array;
}
