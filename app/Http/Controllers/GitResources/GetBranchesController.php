<?php

namespace App\Http\Controllers\GitResources;

use App\Http\Requests\GetBranchesRequest;
use App\Services\GithubService;
use App\Http\Controllers\Controller;
class GetBranchesController extends Controller
{
    /**
     *  List all branches from repository
     */
    public function __invoke(GetBranchesRequest $request, GithubService $git)
    {
        return $git->branches()->getBranches($request['owner'], $request['repository']);
    }
}
