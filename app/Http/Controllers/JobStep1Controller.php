<?php

namespace App\Http\Controllers;

use App\Services\GithubService;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\JobStep1Request;

class JobStep1Controller extends Controller
{
    /**
     * First step for job creation
     * @return \Illuminate\Contracts\View\View
     */
    public function index(): \Illuminate\Contracts\View\View
    {
        return view('jobs.createjob1');
    }

    /**
     * Try to retrieve the repository from GitHub
     */
    public function create(JobStep1Request $request, GitHubService $git)
    {
        session(['job_repository' => $request->validated()]);

        try {
            $items = $git->getPhpFilesFromTree(session('job_repository'));
        } catch (\Exception $e) {
            Log::error("Error getting response from repository from API {$e->getMessage()}", $request->toArray());
            return back()->withError($e->getMessage())->withInput();
        }

        session(['job_items' => $items]);

        return redirect()->route('codeanalyzer.create.step.two');
    }
}
