<?php

namespace App\Policies;

use App\Models\Jobitem;
use App\Models\User;

class JobitemPolicy
{
    /**
     * Create a new policy instance.
     */
    public function create(User $user, Jobitem $jobitem): bool
    {
        return $user->id == $jobitem->job->user_id;
    }
}
