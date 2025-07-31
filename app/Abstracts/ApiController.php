<?php

namespace App\Abstracts;

use App\Enums\HttpStatus;
use App\Enums\HttpError;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use App\Exceptions\AuthorizationException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ValidationException;
use App\Exceptions\UnknownStatusException;
use App\Exceptions\InternalServerException;
use App\Exceptions\ForbiddenException;

abstract class ApiController
{
    protected $apiKey;

    protected $owner;

    protected $repository;

    protected $branch;

    public function __construct(string $apiKey = '')
    {
        $this->apiKey = $apiKey;
    }

    public function setApiKey(string $apiKey = '')
    {
        $this->apiKey = $apiKey;
    }
    public function setRepository(string|array $repository, string|null $owner = null, string|null $branch = null)
    {
        if (is_array($repository)) {
            $this->owner = $repository['owner'];
            $this->repository = $repository['repository'];
            $this->branch ?? $repository['branch'];
        } else {
            $this->owner = $owner;
            $this->repository = $repository;
            $this->branch ?? $branch;
        }
    }

    public function get(string $uri, array|string|null $query = null)
    {
        return $this->handleResponse(Http::withToken($this->apiKey)->get($uri, $query));
    }

    public function post(string $uri, array $data = [])
    {
        return $this->handleResponse(Http::withToken($this->apiKey)->post($uri, $data));
    }

    protected function handleResponse(Response $response)
    {
        $status = HttpStatus::from($response->getStatusCode());

        if (!($type = $status->hasError())) {
            return $status === HttpStatus::NoContent ? null : $response->json();
        }

        if ($type === HttpError::Server) {
            throw new InternalServerException();
        }

        throw match ($status) {
            HttpStatus::AuthFailed => new AuthorizationException(),
            HttpStatus::UnAuthorized => new ForbiddenException(),
            HttpStatus::ResourceNotFound => new ResourceNotFoundException(),
            HttpStatus::BadRequest => new ValidationException(),
            HttpStatus::Conflict => new ValidationException(),
            HttpStatus::UnprocessableEntity => new ValidationException(),
            default => new UnknownStatusException($status->value)
        };
    }

    /**
     * Function to return an exception based on a HTTP status code
     *
     * @param  int  $status  HTTP Status Code
     */
    /* private function handleStatus(Response $http): Exception|array|null
    {
        return match (Status::from($http->status())) {
            Status::OK => $http->json(),
            Status::Created => $http->json(),
            Status::NoContent => null,
            Status::AuthFailed => new AuthorizationException(trans('exceptions.auth_failed')),
            Status::ResourceNotFound => new ResourceNotFoundException(trans('exceptions.resource_missing')),
            Status::Conflict => new ConflictException(trans('exceptions.conflict')),
            Status::IssuesDisable => new Exception(trans('exceptions.issues_disabled')),
            Status::ValidationError => new ValidationException(trans('exceptions.validation')),
            default => new Exception(trans('exceptions.unknown'))
        };
    } */
}
