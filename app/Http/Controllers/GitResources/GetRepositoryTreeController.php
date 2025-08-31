<?php

namespace App\Http\Controllers\GitResources;

use App\Services\GitHubApi\GithubService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Utilities\TreeBuilder;

class GetRepositoryTreeController extends Controller
{
    /**
     *  Get all files from the requested repository tree
     */
    public function __invoke(Request $request, GithubService $git)
    {
        $args = $request->validate([
            'owner' => 'required|string|max:39',        // 39 character limit by GitHub
            'repository' => 'required|string|max:100',  // 100 character limit
            'branch' => 'required|string|max:250'       // 250 character limit with UTF-8 encoding (lower with other)
        ]);

        $items = $git->data()->trees()->getTree($args);

        return TreeBuilder::buildNodes($items);
    }
}
