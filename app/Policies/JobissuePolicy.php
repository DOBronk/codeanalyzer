<?php

namespace App\Policies;

use App\Models\Jobissue;
use App\Models\User;

class JobissuePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Jobissue $jobissue): bool
    {
        return $user->id == $jobissue->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Jobissue $itemid): bool
    {
        return $user->id == $itemid->job->user_id;
    }
}
