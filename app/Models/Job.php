<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Traits\UserScope;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jobs extends Model
{
    use UserScope;
    protected $fillable = ['user_id', 'owner', 'repository', 'active', 'branch'];
    protected $table = 'codeanalyzer_jobs';

    public function items()
    {
        return $this->hasMany(Jobitem::class, "job_id");
    }

    public function scopeActiveJobs(Builder $query): Builder
    {
        return $query->where('active', '=', '1');
    }
}
