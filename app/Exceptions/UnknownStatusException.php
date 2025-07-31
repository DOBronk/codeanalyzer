<?php

namespace App\Exceptions;

class UnknownStatusException  extends \Exception
{
    protected function __construct(public readonly int $statuscode, string $message = "")
    {
        parent::__construct($message);
    }
}
