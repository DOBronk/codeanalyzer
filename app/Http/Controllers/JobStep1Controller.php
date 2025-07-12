<?php

namespace App\Http\Controllers;

use App\Services\GithubService;
use App\Utilities\TreeBuilder;
use App\Http\Requests\CreateJobRequest;
use Illuminate\Http\Request;

class JobStep1Controller extends Controller
{
    public function index()
    {
        return view('jobs.createjob1');
    }
    public function create(Request $request, GitHubService $git)
    {
        $validated = $request->validate([
            'owner' => 'required|string|max:255',
            'repository' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
        ]);

        $validated['branch'] ??= 'main';

        try {
            $items = $git->getPhpFilesFromTree($validated['owner'],  $validated['repository'],  $validated['branch']);
        } catch (\Exception $e) {
            return back()->withError($e->getMessage())->withInput();
        }

        $request->session()->put('job_repository', $validated);
        $request->session()->put('job_items', $items);
        
        return redirect()->route('codeanalyzer.create.step.two');
    }
}
