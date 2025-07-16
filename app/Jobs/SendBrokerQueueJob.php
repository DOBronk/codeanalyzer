<?php

namespace App\Jobs;

use App\DTO\JobDTO;
use App\Events\BrokerQueueError;
use App\Models\Job;
use App\Models\User;
use App\Services\GithubService;
use App\Services\MessageBroker;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendBrokerQueueJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly Job $userjob, private readonly string $api) {}

    /**
     * Execute the job.
     */
    public function handle(GithubService $git, MessageBroker $broker): void
    {
        try {
            $tasks = [];
            $job = $this->userjob;

            $job->items()->each(function ($item) use ($git, &$tasks, $job) {
                $code = $git->getBlob($job->owner, $job->repository, $item->sha, $this->api);
                $tasks[] = JobDTO::make($job->id, $job->user_id, $item->id, $code)->toJson();
            }, 100);

            $broker->addJobs($tasks);
        } catch (\Exception $e) {
            BrokerQueueError::dispatch($job, $e->getMessage(), User::find($job->user_id));
        }
    }
}
