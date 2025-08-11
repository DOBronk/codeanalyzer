<?php

namespace App\Enums;

enum HttpStatus: int
{
    case OK = 200;
    case Created = 201;
    case NoContent = 204;
    case BadRequest = 400;
    case AuthFailed = 401;
    case UnAuthorized = 403;
    case ResourceNotFound = 404;
    case RequestTimeout = 408;
    case Conflict = 409;
    case IssuesDisable = 410;
    case UnprocessableEntity = 422;
    case TooManyRequests = 429;
    case InternalServerError = 500;
}
