<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Services\GithubService;
use App\Services\MessageBroker;
use App\Models\Jobs;
use App\Models\User;
use App\DTO\JobDTO;
use App\Events\BrokerQueueError;


class SendBrokerQueueJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly Jobs $userjob)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(GithubService $git, MessageBroker $broker): void
    {
        try {
            foreach ($this->userjob->items as $item) {
                $code = $git->getBlob($this->userjob->owner, $this->userjob->repo, $item->blob_sha, User::find($this->userjob->user_id)->settings->gh_api_key);
                $task = new JobDTO($this->userjob->id, $this->userjob->user_id, $item->id, $code);
                $broker->addJob($task->toJson());
            }
        } catch (\Exception $e)
        {
            BrokerQueueError::dispatch($this->userjob, $e->getMessage(), User::find($this->userjob->user_id));
        }
    }
}
