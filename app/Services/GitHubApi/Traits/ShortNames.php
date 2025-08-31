<?php

namespace App\Services\GitHubApi\Traits;

trait ShortNames
{
    protected function shortClassName(object $object)
    {
        $fqclass = get_class($object);
        return substr($fqclass, strrpos($fqclass, '\\') + 1);
    }
}
