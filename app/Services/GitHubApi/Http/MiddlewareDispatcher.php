<?php

namespace App\Services\GitHubApi\Http;

use App\Services\GitHubApi\Traits\ShortNames;
use ReflectionMethod;

class MiddlewareDispatcher
{
    use ShortNames;
    public function dispatch($param, array $modules, array $params = []): mixed
    {
        foreach ($modules as $module) {
            if ($handler = $this->mapHandler($param, $module)) {
                $param_count = (new ReflectionMethod($module::class, $handler))->getNumberOfParameters();
                $param = ($param_count > 1 ? $module->{$handler}($param, $params) : $module->{$handler}($param)) ?? $param;
            }
        }

        return $param;
    }

    private function mapHandler($param, $module): ?string
    {
        $method = $this->getMethodName($param);
        return method_exists($module, $method) ? $method : null;
    }
}
