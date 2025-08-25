<?php

namespace App\Http\Controllers\GitResources;

use Illuminate\Http\Request;
use App\Services\GithubService;
use App\Http\Controllers\Controller;

class GetBranchesController extends Controller
{
    /**
     *  List all branches from repository
     */
    public function __invoke(Request $request, string $owner, string $repository, GithubService $git)
    {
        return $git->branches()->getBranches($owner, $repository);
    }
}
