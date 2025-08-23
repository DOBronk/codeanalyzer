<?php

namespace App\Services\GitHubApi\Contracts;

use Illuminate\Http\Client\Response;

interface HandlesFinal
{
    public function handleFinal(Response $response): Response;
}
