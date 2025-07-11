<?php

namespace App\Policies;

use App\Models\Jobissues;
use App\Models\Jobitems;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JobissuesPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Jobissues $jobissues): bool
    {
        return $user->id == $jobissues->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Jobissues $itemid): bool
    {
        return $user->id == $itemid->job->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Jobissues $jobissues): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Jobissues $jobissues): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Jobissues $jobissues): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Jobissues $jobissues): bool
    {
        return false;
    }
}
