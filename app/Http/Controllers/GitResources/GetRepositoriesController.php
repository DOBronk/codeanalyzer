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
    { //required|string|max:39|regex:/([A-Z|0-9|-])/'
        $request->validate(['owner' => ['required', 'string', 'max:39', "not_regex:/([^A-Z|0-9|-])/i"]]); // 39 character limit by GitHub

        return $git->repositories()->getRepositories($request['owner']);
    }
}
