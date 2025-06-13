<?php

namespace App\Listeners;

use App\Events\BrokerQueueError;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Jobs;

class SetJobErrorStatus
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BrokerQueueError $event): void
    {
        $event->job->update(['active' => 2]);

        // TODO: Mail versturen
    }
}
