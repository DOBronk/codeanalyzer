<?php

namespace App\Services;

enum EnumStatus: int
{
    case OK = 200;
    case Created = 201;
    case NoContent = 204;
    case AuthFailed = 401;
    case ResourceNotFound = 404;
    case Conflict = 409;
    case IssuesDisable = 410;
    case ValidationError = 422;
}
