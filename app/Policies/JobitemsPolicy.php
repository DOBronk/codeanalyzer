<?php

namespace App\Policies;

use App\Models\Jobitems;
use App\Models\Jobs;
use App\Models\User;

class JobitemsPolicy
{
    /**
     * Create a new policy instance.
     */
    public function create(User $user, Jobitems $jobitems): bool
    {
        return $user->id == $jobitems->job->user_id;
    }
}
