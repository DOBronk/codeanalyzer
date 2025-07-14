<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait UserScope
{
    #[Scope]
    protected function currentUser(Builder $query): void
    {
        $query->where('user_id', '=', Auth::id());
    }
}
