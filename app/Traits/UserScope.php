<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait UserScope
{
    #[Scope]
    protected function currentUser(Builder $query): void
    {
        $query->where('user_id', '=', Auth::id());
    }
}
