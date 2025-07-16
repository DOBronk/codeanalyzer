<?php

namespace App\Policies;

use App\Models\Job;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JobPolicy
{
    public function noActiveJobs()
    {
        return Job::query()->currentUser()->activeJobs()->count()
            ? Response::deny('Not allowed while a job is active') : Response::allow();
    }

    public function view(?User $user, Job $job)
    {
        return $user->id === $job->user_id;
    }
}
