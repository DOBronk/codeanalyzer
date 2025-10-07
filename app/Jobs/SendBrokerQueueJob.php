<?php

namespace App\Jobs;

use App\DTO\JobDTO;
use App\Events\BrokerQueueError;
use App\Models\Job;
use App\Services\GitHubApi\GithubService;
use App\Services\MessageBroker;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendBrokerQueueJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private Job $userjob) {}

    /**
     * Execute the job.
     */
    public function handle(GithubService $git, MessageBroker $broker): void
    {
        try {
            [$owner, $repository, $user, $tasks] = [$this->userjob->owner, $this->userjob->repository, $this->userjob->load(['user'])->user, []];
            $git->config()->apiKey = $this->userjob->user->settings->gh_api_key;
            $this->userjob->items()->each(function ($item) use ($git, $owner, $repository, &$tasks) {
                $code = $git->data()->blobs()->getBlob($item->sha, $owner, $repository);
                $tasks[] = JobDTO::make($this->userjob->id, $this->userjob->user->id, $item->id, $code)->toJson();
            }, 100);

            $broker->addJobs($tasks);
        } catch (\Exception $e) {
            report($e);
            BrokerQueueError::dispatch($this->userjob, $user);
        }
    }
}
