<?php

namespace App\Services\GitHubApi\Abstracts;

use App\Services\GitHubApi\Contracts\HttpModuleInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class HttpModule implements HttpModuleInterface
{
    protected $requestHandle = "handleRequest";
    protected $responseHandle = "handleResponse";

    private function getHandler(string $handle): ?callable
    {
        if (method_exists($this, $handle)) {
            return [$this, $handle];
        }
        return null;
    }
    public function getResponseHandler(): callable
    {
        return $this->getHandler($this->responseHandle)
            ?? fn(ResponseInterface $response) => $response;
    }
    public function getRequestHandler(): callable
    {
        return $this->getHandler($this->requestHandle)
            ?? fn(RequestInterface $request) => $request;
    }
}
