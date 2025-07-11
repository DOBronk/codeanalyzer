<?php

namespace App\Http\Controllers;

use App\Services\GithubService;
use App\Utilities\TreeBuilder;
use App\Http\Requests\CreateJobRequest;

class JobStep1Controller extends Controller
{
    public function __invoke(CreateJobRequest $request, GitHubService $git)
    {
        $branch = $request['branch'] ?? 'main';

        try {
            $items = $git->getPhpFilesFromTree($request['owner'],  $request['repo'], $branch);
        } catch (\Exception $e) {
            return back()->withError($e->getMessage())->withInput();
        }

        session(['createjobvalues' => ['branch' => $branch, ...$request->validated()]]);

        return view('jobs.createjob2', [
            'branch' => $branch,
            'items' => TreeBuilder::buildTree($items),
            ...$request->validated(),
        ]);
    }
}
