<?php

namespace App\Services\GitHubApi\Repositories;

use App\Services\GitHubApi\Abstracts\ApiController;
use Illuminate\Support\Facades\Log;

class Repositories extends ApiController
{
    public function getRepositories(string $owner)
    {
        $data = $this->get("/users/{$owner}/repos", params: ['filters' => ['columns' => ['name', 'default_branch']]])->json();
        //     $stream = fopen('test.json', 'x+');

        //     fwrite($stream, $data);

        //     fclose($stream);

        //     Log::info("wrote file");
        //  $size = $data->getBody()->getSize();

        //   $data_priv =  $this->get("/search/repositories", ['q' => "user:$owner is:private"])->json();

        //    if (array_key_exists('total_count', $data_priv) && $data_priv['total_count'] > 0) {
        //       $data = array_merge($data, $data_priv['items']);
        //    }
        //
        $repositories = array_column($data, 'name');
        $default_branches = array_column($data, 'default_branch');

        // Log::info('Repositories: ' . count($repositories));
        //    array_multisort($repositories, SORT_NATURAL | SORT_FLAG_CASE, $default_branches);

        return [$repositories, $default_branches];
    }

    public function getRepository(string $owner, string $repository)
    {
        return $this->get("/repos/{$owner}/{$repository}");
    }
}
