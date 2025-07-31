<?php

namespace App\Models;

use App\Traits\UserScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Job extends Model
{
    use UserScope, HasFactory;

    protected $fillable = ['user_id', 'owner', 'repository', 'active', 'branch'];

    protected $table = 'codeanalyzer_jobs';

    public function items()
    {
        return $this->hasMany(Jobitem::class, 'job_id');
    }

    public function scopeActiveJobs(Builder $query): Builder
    {
        return $query->where('active', '=', '1');
    }
}
