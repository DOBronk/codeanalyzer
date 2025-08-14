<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Jobs\SendBrokerQueueJob;
use App\Models\Job;
use Inertia\Inertia;
use Exception;

class CreateJobController extends Controller
{
    /**
     * First step for job creation
     */
    public function index()
    {
        return Inertia::render(
            'treeviewer',
            ['csrf' => csrf_token(), 'route' => route('codeanalyzer.createjob.post')]
        )
            ->withViewData(['vueHeader' => trans('job.create')]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'selections' => ['required', 'array', 'min:1'],
        ]);

        DB::beginTransaction();

        try {
            $job = Job::create(['user_id' => $request->user()->id, ...$request->all()]);
            $job->items()->createMany($request->selections);

            SendBrokerQueueJob::dispatch($job->load('user'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error: {$e->getMessage()}", ['session' => $request]);

            return back()->withError('Kon job niet aanmaken!');
        }

        DB::commit();

        return redirect()->route('codeanalyzer.index');
    }
}
