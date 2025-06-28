<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\SendBrokerQueueJob;
use Illuminate\Support\Facades\DB;
use App\Models\Jobs;
use App\Services\GithubService;
use App\Utilities\TreeBuilder;
use App\Http\Requests\CreateJobRequest;
use Illuminate\Support\Facades\Validator;

class CodeAnalyzerController extends Controller
{
    /**
     * Show first step of multi-step create job form
     */
    public function createStepOne()
    {
        return view('jobs.createjob1');
    }
    /**
     * Show second step of multi-step create job form
     */
    public function createStepTwo(CreateJobRequest $request, GitHubService $git)
    {
        $data = $request->validated();

        $repo = $data['repository'];
        $owner = $data['owner'];
        $branch = $data['branch'] ?? 'main';

        try {
            $items = $git->getPhpFilesFromTree($owner, $repo, $branch, $request->user()->settings->gh_api_key);
        } catch (\Exception $e) {
            return redirect()->back()->withError($e->getMessage());
        }

        return view('jobs.createjob2', [
            'owner' => $owner,
            'repository' => $repo,
            'branch' => $branch,
            'items' => TreeBuilder::buildTree($items),
        ]);
    }
    /**
     * Handle first step of multi-step create job form
     */
    public function postCreateStepOne(CreateJobRequest $request)
    {
        $data = $request->validated();

        return redirect()->route('codeanalyzer.create.step.two', $data);
    }
    /**
     * Handle second step of multi-step create job form
     */
    public function postCreateStepTwo(CreateJobRequest $request)
    {
        $data = $request->validated();

        if (!$request->selectedItems) {
            return redirect()->route('codeanalyzer.create.step.two', $data)->with('error', 'Please select at least one file');
        }

        DB::transaction(function () use ($request, $data) {
            $job = Jobs::create([
                'user_id' => $request->user()->id,
                'owner' => $data['owner'],
                'repo' => $data['repository'],
                'active' => 1
            ]);

            foreach ($request->selectedItems as $item) {
                $values = explode("|", $item);
                $job->items()->create([
                    'path' => $values[1],
                    'blob_sha' => $values[0],
                    'status_id' => 0,
                    'results' => "0"
                ]);
            }

            dispatch(new SendBrokerQueueJob($job));
        });

        return redirect()->route("codeanalyzer.index");
    }
}
