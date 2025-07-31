<?php

namespace App\Enums;

enum HttpError
{
    case Client;
    case Server;

    public static function fromStatus(int $status)
    {
        return $status < 500 ? HttpError::Client : HttpError::Server;
    }
}
