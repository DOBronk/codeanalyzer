<?php

namespace App\Policies;

use App\Models\Jobs;
use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class JobsPolicy
{
    public function noActiveJobs()
    {
        return Jobs::query()->currentUser()->activeJobs()->count()
            ? Response::deny("Not allowed while a job is active") : Response::allow();
    }

    public function view(?User $user, Jobs $job)
    {
        return $user->id === $job->user_id;
    }
}
