<?php

namespace App\Services\GitHubApi\GitData;

use App\Services\GitHubApi\Abstracts\ApiController;

class Trees extends ApiController
{
    /**
     * Recursively retrieve all files ending with .php from provided git tree
     *
     * @param array|string Owner of repository or can be an associative array with all values
     * @param  string  $repository  Repository name
     * @param  string  $sha  Branch name or SHA for branch
     * @return array Returns an array with filepath as key and corresponding SHA
     */
    public function getTree(array $array): array
    {
        [$owner, $repository, $branch] = [$array['owner'], $array['repository'], $array['branch']];
        $response = $this->get("/repos/{$owner}/{$repository}/git/trees/{$branch}", ['recursive' => 1])->json();

        if (is_array($response) && key_exists('tree', $response)) {
            return $response['tree'];
        }

        abort(404);
    }
}
