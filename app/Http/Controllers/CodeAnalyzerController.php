<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\SendBrokerQueueJob;
use Illuminate\Support\Facades\DB;
use App\Models\Jobs;
use App\Services\GithubService;
use App\Utilities\TreeBuilder;

class CodeAnalyzerController extends Controller
{
    public function createStepOne()
    {
        return view('jobs.createjob1');
    }
    public function createStepTwo(Request $request, GitHubService $git)
    {
        $data = $request->validate([
            'owner' => 'required|string|max:255',
            'repository' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
        ]);

        $repo = $data['repository'];
        $owner = $data['owner'];
        $branch = $data['branch'] ?? 'main';

        try {
            $items = $git->getPhpFilesFromTree($owner, $repo, $branch);
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

    public function postCreateStepOne(Request $request)
    {
        $data = $request->validate([
            'owner' => 'required|string|max:255',
            'repository' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
        ]);

        return redirect()->route('codeanalyzer.create.step.two', [
            'owner' => $data['owner'],
            'repository' => $data['repository'],
            'branch' => $data['branch']
        ]);
    }
    public function postCreateStepTwo(Request $request)
    {
        $data = $request->validate([
            'owner' => 'required|string|max:255',
            'repository' => 'required|string|max:255',
            'selectedItems.*' => 'required',
        ]);

        DB::transaction(function () use ($request, $data) {
            $repo = $data['repository'];
            $owner = $data['owner'];

            $job = Jobs::create([
                'user_id' => $request->user()->id,
                'owner' => $owner,
                'repo' => $repo,
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

    public function showDetails($id)
    {
        $job = Jobs::find($id);

        if (!isset($job)) {
            abort(404);
        }
        return view('jobs.jobdetails', ['job' => $job]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobs = Jobs::query()->currentUser()->with('items')->get();

        return view('index', ['items' => $jobs]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
    }

    /**
     * Show the specified resource.
     */
    public function show()
    {
        return view('codeanalyzer::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        return view('codeanalyzer::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update()
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        //
    }

}
