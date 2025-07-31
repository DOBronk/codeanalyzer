<?php

namespace App\Policies;

use App\Models\Jobitem;
use App\Models\User;

class JobitemPolicy
{
    /** Validates if user can create issue for this job and verifies if the status is OK (no issue created already etc.) */
    public function create(User $user, Jobitem $jobitem): bool
    {
        return $user->id == $jobitem->job->user_id && $jobitem->status_id == 1;
    }
}
