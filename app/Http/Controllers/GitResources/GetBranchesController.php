<?php

namespace App\Http\Controllers\GitResources;

use App\Services\GithubService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class GetBranchesController extends Controller
{
    /**
     *  List all branches from repository
     */
    public function __invoke(Request $request, GithubService $git)
    {
        $request->validate([
            'owner' => 'required|string|max:39',        // 39 character limit by GitHub
            'repository' => 'required|string|max:100'   // 100 character limit
        ]);

        return $git->git()->branches()->getBranches($request->owner, $request->repository);
    }
}
