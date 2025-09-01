<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Jobs\SendBrokerQueueJob;
use App\Models\Job;
use Inertia\Inertia;
use Exception;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Support\Str;

class CreateJobController extends Controller
{
    /**
     * First step for job creation
     */
    public function index(ViewErrorBag $bag)
    {
        $errors = null;

        if (session()->has('errors')) {
            $bag  = session('errors');
            $errors = [];

            foreach ($bag->getMessages() as $error) {
                $errors[] = $error;
            }
        }

        return Inertia::render(
            'treeviewer',
            ['csrf' => csrf_token(), 'error' => $errors, 'route' => route('codeanalyzer.createjob.post')]
        )
            ->withViewData(['vueHeader' => trans('job.create')]);
    }
    public function store(StoreJobRequest $request): RedirectResponse
    {
        $redirect = redirect()->route('codeanalyzer.index');

        try {
            DB::beginTransaction();
            $job = Job::create(['user_id' => Auth::id(), ...$request->validated()]);
            $job->items()->createMany($request['selections']);
            SendBrokerQueueJob::dispatch($job->load('user'));
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);
            $redirect = $redirect->with('trouble', trans('job.createfailed'));
        } finally {
            return $redirect;
        }
    }
}
