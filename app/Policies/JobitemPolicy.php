<?php

namespace App\Policies;

use App\Models\Jobitem;
use App\Models\Job;
use App\Models\User;

class JobitemsPolicy
{
    /**
     * Create a new policy instance.
     */
    public function create(User $user, Jobitem $jobitems): bool
    {
        return $user->id == $jobitems->job->user_id;
    }
}
