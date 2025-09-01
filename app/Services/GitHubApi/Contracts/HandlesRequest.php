<?php

namespace App\Services\GitHubApi\Contracts;

use Psr\Http\Message\RequestInterface;

interface HandlesRequest
{
    public function handleRequest(RequestInterface $request): RequestInterface;
}
