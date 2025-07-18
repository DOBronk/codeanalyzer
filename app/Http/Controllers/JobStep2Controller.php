<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobRequest;
use App\Jobs\SendBrokerQueueJob;
use App\Models\Job;
use App\Utilities\TreeBuilder;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JobStep2Controller extends Controller
{
    /**
     * Build a multi-dimensional array for rendering the tree and pass to the view
     */
    public function index(): \Illuminate\Contracts\View\View
    {
        $items = TreeBuilder::buildTree(session('job_items'));

        return view('jobs.createjob2', compact('items'));
    }

    /**
     * Store a new job in the database with the selected files, dispatch a job to
     * fetch all the file contents from github and send to each the message broker
     */
    public function store(StoreJobRequest $request): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $job = Job::create(session('job_repository'));

            $job->items()->createMany(array_intersect_key(
                session('job_items'),
                array_flip($request->selectedItems)
            ));

            SendBrokerQueueJob::dispatch($job, $request->user()->settings->gh_api_key);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error: {$e->getMessage()}", ['session' => $request]);

            return back()->withError('Kon job niet aanmaken!');
        }

        DB::commit();

        session()->forget(['job_items', 'job_repository']);

        return redirect()->route('codeanalyzer.index');
    }
}
