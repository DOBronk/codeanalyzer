<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateJobRequest;
use App\Jobs\SendBrokerQueueJob;
use Illuminate\Support\Facades\DB;
use App\Models\Jobs;
use Illuminate\Http\Request;
use App\Utilities\TreeBuilder;
use Exception;

class JobStep2Controller extends Controller
{
    private function checkSession(Request $request): void
    {
        if (!$request->session()->has('job_items') || !$request->session()->has('job_repository')) {
            abort(422, 'Session variables missing');
        }
    }
    public function index(Request $request)
    {
        $this->checkSession($request);
        $items = TreeBuilder::buildTree($request->session()->get('job_items'));

        return view('jobs.createjob2',  compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate(['selectedItems' => ['required', 'array', 'min:1']]);
        $this->checkSession($request);

        try {
            DB::transaction(function () use ($request) {
                $job = Jobs::create([
                    'user_id' => $request->user()->id,
                    ...$request->session()->get("job_repository")
                ]);
                $jobitems = $request->session()->get("job_items");
                $job->items()->createMany(array_map(fn($item) => $jobitems[$item], $request->selectedItems));
                SendBrokerQueueJob::dispatch($job);
                $request->session()->forget(['job_items', 'job_repository']);
            });
        } catch (Exception $e) {
            return back()->withError("Kon job niet aanmaken!");
        }

        return redirect()->route("codeanalyzer.index");
    }
}
