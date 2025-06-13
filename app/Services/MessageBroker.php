<?php

namespace App\Services;

abstract class MessageBroker
{
    public function __construct(
        protected string $host,
        protected int $port,
        protected string $username,
        protected string $password,
        protected string $queue = "jobs"
    ) {
    }
    abstract public function addJob(string $message);
}
