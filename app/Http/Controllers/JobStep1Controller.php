<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobStep1Request;
use App\Services\GithubService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use App\Utilities\TreeBuilder;
use Inertia\Inertia;

class JobStep1Controller extends Controller
{
    /**
     * First step for job creation
     */
    public function index()
    {
        return self::newCreator(); // view('jobs.createjob1');
    }

    public static function newCreator()
    {
        return Inertia::render(
            'treeviewer',
            ['csrf' => csrf_token(), 'route' => route('codeanalyzer.create.step.two.post')]
        )
            ->withViewData(['vueHeader' => trans('job.create')]);
    }
    /**
     * Try to retrieve the repository from GitHub
     */
    public function create(JobStep1Request $request, GithubService $git): RedirectResponse
    {
        $repository = $request->validated();

        try {
            $items = $git->gitDatabase()->repositories()->getRepositoryFiles($repository);
            dd(TreeBuilder::buildTree5(array_filter($items, fn($item) => $item['type'] !== 'blb' && !str_ends_with($item['path'], '.php121'))));
        } catch (\Exception $e) {
            Log::error("Error getting response from repository from API {$e->getMessage()}", $request->toArray());

            return back()->withError($e->getMessage())->withInput();
        }

        session()->put('job_items', $items);
        session()->put('job_repository', $request->validated());

        return redirect()->route('codeanalyzer.create.step.two');
    }
}
