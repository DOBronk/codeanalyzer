<?php

namespace App\Services\GitHubApi\Abstracts;

use App\Services\GitHubApi\Contracts\HttpModuleInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class HttpModule implements HttpModuleInterface
{
    protected $requestHandle;
    protected $responseHandle;
    public function getResponseHandler(): callable
    {
        if (method_exists($this, $responseHandle ?? "handleResponse")) {
            return [$this, $responseHandle ?? 'handleResponse'];
        }

        return fn(ResponseInterface $response) => $response;
    }
    public function getRequestHandler(): callable
    {
        if (method_exists($this, $requestHandle ?? 'handleRequest')) {
            return [$this, $requestHandle ?? 'handleRequest'];
        }
        return fn(RequestInterface $request) => $request;
    }
}
