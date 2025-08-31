<?php

namespace App\Services\GitHubApi\Issues;

use App\Services\GitHubApi\Abstracts\ApiController;

class Issues extends ApiController
{
    public function createIssue(string $owner, string $repository, string $title, string $body, array $labels = ['AI Generated Issue']): string
    {
        $response = $this->post("/repos/{$owner}/{$repository}/issues", [
            'title' => $title,
            'body' => $body,
            'labels' => $labels,
        ]);

        return $response- $response['html_url'];
    }
}
