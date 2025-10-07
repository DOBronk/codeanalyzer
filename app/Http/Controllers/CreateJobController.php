<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\ResourceResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Jobs\SendBrokerQueueJob;
use App\Models\Job;
use App\Services\GitHubApi\GithubService;
use Inertia\Inertia;
use Illuminate\Support\Arr;
use Exception;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Support\Str;
use App\Services\GitHubApi\Http\Modules\EtagCache;
use Illuminate\Support\Facades\Cache;

class CreateJobController extends Controller
{
    private int $log_interval = 500;
    public function benchmark(Request $request, GithubService $git)
    {
        // Tickle redis a little, use json_encode for PHP
        $data = Cache::remember('jobs', 5, function () {
            return Job::with('items')
                #          ->orderByDesc('created_at')
                ->select('users.id', 'users.name', 'users.email', 'users.remember_token', 'user_id', 'owner', 'repository', 'active', 'branch')
                ->join('users', 'codeanalyzer_jobs.user_id', '=', 'users.id')->get()->toArray();
        });
        // Tickle redis some more with a check, read and write and log on log_interval
        if ($this->shouldLog()) {
            Log::debug("Benchmark page served $this->log_interval requests");
        }
        // Eager load mysql columns with all items and ordering encode as Json and respond add in an extra join;
        return $data;
        /*  return view(
        'jobs.index-bm',
        [
            'items' =>
            Job::with('items')
                ->orderByDesc('created_at')
                ->select()
                ->paginate(10)
        ]
    );*/
    }
    private function shouldLog(): bool
    {
        $count = Cache::has('bm_counter') ? (int)Cache::get('bm_counter') + 1 : 1;
        $answer = $count < $this->log_interval ? $count : 0;

        Cache::put('bm_counter',  $answer, 5); // 5 Sec TTL zodat waarde 5 sec na benchmark reset

        return $answer === 0;
    }
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
            $redirect = $redirect->with('error', trans('job.createfailed'));
        } finally {
            return $redirect;
        }
    }
}
