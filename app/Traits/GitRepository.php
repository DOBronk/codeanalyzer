<?php

namespace App\Traits;

trait GitRepository
{
    protected $owner;
    protected $repository;
    protected $branch;

    public function setRepositoryArray(array $repository)
    {
        $this->owner = $repository['owner'];
        $this->repository = $repository['repository'];
        $this->branch = $repository['branch'] ?? 'main';
    }

    public function setRepository(string $repository, string $owner, string $branch = 'main')
    {
        $this->setRepositoryArray(['repository' => $repository, 'owner' => $owner, 'branch' => $branch]);
    }

    public function isEmpty(): bool
    {
        return empty($owner) && empty($repository) && empty($branch);
    }
}
