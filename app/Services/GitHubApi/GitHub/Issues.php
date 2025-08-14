<?php

namespace App\Services\GitHubApi\GitHub;

use App\Services\GitHubApi\Abstracts\ApiController;

class Issues extends ApiController
{
    public function createIssue(string $owner, string $repository, string $title, string $body): string
    {
        $response = $this->post("/repos/{$owner}/{$repository}/issues", [
            'title' => $title,
            'body' => $body,
            'labels' => ['AI Generated Issue'],
        ]);

        return $response['html_url'];
    }
}
