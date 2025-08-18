<?php

namespace App\Services\GitHubApi\Contracts;

interface HttpModuleInterface
{
    public function getResponseHandler(): callable;
    public function getRequestHandler(): callable;
}
