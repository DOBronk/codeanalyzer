<?php

namespace App\Interfaces;

interface IGithubService
{
    public function getRepository(array $array, string $extension = ".php");
    public function getBlob(string $sha);
    public function createIssue(string $owner, string $repository, string $title, string $body);
    public function setRepository(string $repository, string $owner, string $branch = 'main');
    public function setRepositoryArray(array $repository);
    public function setApi(string $api);
}
