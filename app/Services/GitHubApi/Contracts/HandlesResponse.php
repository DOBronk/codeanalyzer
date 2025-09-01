<?php

namespace App\Services\GitHubApi\Contracts;

use Psr\Http\Message\ResponseInterface;

interface HandlesResponse
{
    public function handleResponse(ResponseInterface $response): ResponseInterface;
}
