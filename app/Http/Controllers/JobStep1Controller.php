<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobStep1Request;
use App\Services\GithubService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class JobStep1Controller extends Controller
{
    /**
     * First step for job creation
     */
    public function index(): View
    {
        return view('jobs.createjob1');
    }

    /**
     * Try to retrieve the repository from GitHub
     */
    public function create(JobStep1Request $request, GitHubService $git): RedirectResponse
    {
        $repository = $request->validated();

        try {
            $items = $git->getRepository($repository);
        } catch (\Exception $e) {
            Log::error("Error getting response from repository from API {$e->getMessage()}", $request->toArray());

            return back()->withError('Kon repository niet laden')->withInput();
        }

        session()->put('job_items', $items);
        session()->put('job_repository', $request->validated());

        return redirect()->route('codeanalyzer.create.step.two');
    }
}
