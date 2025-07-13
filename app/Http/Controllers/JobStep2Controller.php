<?php

namespace App\Http\Controllers;

use App\Jobs\SendBrokerQueueJob;
use Illuminate\Support\Facades\DB;
use App\Models\Jobs;
use App\Http\Requests\StoreJobRequest;
use App\Utilities\TreeBuilder;
use Illuminate\Http\RedirectResponse;
use Exception;
use Illuminate\Support\Facades\Log;

class JobStep2Controller extends Controller
{
    /**
     * Check to see if the required session variables are set
     */
    private function checkSession(): void
    {
        if (!session()->has(['job_items', 'job_repository'])) {
            abort(422, 'Session variables missing');
        }
    }

    /**
     * Build a multi-dimensional array for rendering the tree and pass to the view
     */
    public function index(): \Illuminate\Contracts\View\View
    {
        $this->checkSession();

        $items = TreeBuilder::buildTree(session('job_items'));

        return view('jobs.createjob2',  compact('items'));
    }

    /**
     * Store a new job in the database with the selected files, dispatch a job to
     * fetch all the file contents from github and send to each the message broker
     */
    public function store(StoreJobRequest $request): RedirectResponse
    {
        $this->checkSession();

        DB::beginTransaction();

        try {
            $job = Jobs::create(session("job_repository"))
                ->items()
                ->createMany(array_intersect_key(session("job_items"), array_flip($request->selectedItems)));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error: {$e->getMessage()}", ['session' => $request]);
            return back()->withError("Kon job niet aanmaken!");
        }

        DB::commit();
        SendBrokerQueueJob::dispatch($job);

        session()->forget(['job_items', 'job_repository']);

        return redirect()->route("codeanalyzer.index");
    }
}
