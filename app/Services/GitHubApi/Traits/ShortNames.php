<?php

namespace App\Services\GitHubApi\Traits;

trait ShortNames
{
    protected function shortClassName(object|string $object): string
    {
        $fqclass = is_string($object) ? $object : $object::class;
        return substr($fqclass, strrpos($fqclass, '\\') + 1);
    }

    protected function getMethodName(object|string $object): string
    {
        return "handle" . $this->shortClassName($object);
    }
}
