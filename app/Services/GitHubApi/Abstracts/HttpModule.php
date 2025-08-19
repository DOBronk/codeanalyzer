<?php

namespace App\Services\GitHubApi\Abstracts;

use App\Services\GitHubApi\Contracts\HttpModuleInterface;

abstract class HttpModule implements HttpModuleInterface
{
    protected $requestHandle = "handleRequest";
    protected $responseHandle = "handleResponse";

    public string $className {
        get {
            $fqclass = get_class($this);
            return substr($fqclass, strrpos($fqclass, '\\') + 1);
        }
    }
    public function getHandlers(): array
    {
        $className = $this->className;

        return [
            'request' => [$className => $this->getHandler($this->requestHandle)],
            'response' => [$className => $this->getHandler($this->responseHandle)]
        ];
    }
    private function getHandler(string $handle): callable
    {
        if (method_exists($this, $handle)) {
            return [$this, $handle];
        }
        return fn($response) => $response;
    }
}
