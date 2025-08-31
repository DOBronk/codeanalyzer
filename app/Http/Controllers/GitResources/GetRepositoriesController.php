<?php

namespace App\Http\Controllers\GitResources;

use App\Services\GitHubApi\GithubService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class GetRepositoriesController extends Controller
{
    /**
     * List all owner repositories 
     */
    public function __invoke(Request $request, GithubService $git)
    {
        $request->validate(['owner' => 'required|string|max:39']); // 39 character limit by GitHub

        return $git->repositories()->getRepositories($request['owner']);
    }
}
