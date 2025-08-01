<?php

namespace App\Jobs;

use App\DTO\JobDTO;
use App\Events\BrokerQueueError;
use App\Models\Job;
use App\Models\User;
use App\Services\GithubService;
use App\Interfaces\IGithubService;
use App\Services\MessageBroker;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBrokerQueueJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private Job $userjob) {}

    /**
     * Execute the job.
     */
    public function handle(IGithubService $git, MessageBroker $broker): void
    {
        try {
            $tasks = [];
            $git->setRepositoryArray($this->userjob->toArray());
            $git->setApi($this->userjob->load(['user'])->user->settings->gh_api_key);

            $this->userjob->items()->each(function ($item) use ($git, &$tasks) {
                $code = $git->getBlob($item->sha);
                $tasks[] = JobDTO::make($item, $code)->toJson();
            }, 100);

            $broker->addJobs($tasks);
        } catch (\Exception $e) {
            Log::critical("Array omzetten", $this->userjob->toArray());
            BrokerQueueError::dispatch($this->userjob, $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile() . ' ' . $e->getTraceAsString() . "  :  ", $this->userjob->load(['user'])->user);
        }
    }
}
