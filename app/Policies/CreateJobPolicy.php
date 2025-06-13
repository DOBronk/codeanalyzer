<?php

namespace App\Policies;

use App\Models\Jobs;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CreateJobPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function noActiveJobs()
    {
        return Jobs::query()->currentUser()->activeJobs()->count()
            ? Response::deny("Not allowed while a job is active.") : Response::allow();
    }
}
