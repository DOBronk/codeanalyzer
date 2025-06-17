<?php

namespace App\Listeners;

use App\Events\BrokerQueueError;
use App\Mail\JobErrorMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Jobs;
use Illuminate\Support\Facades\Mail;

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
        
        Mail::to($event->user)->send(new JobErrorMail($event->job, $event->error));
    }
}
