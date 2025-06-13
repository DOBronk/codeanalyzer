<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;

class Jobs extends Model
{
    protected $fillable = ['user_id', 'owner', 'repo', 'active'];
    protected $table = 'codeanalyzer_jobs';

    public function items()
    {
        return $this->hasMany(Jobitems::class, "job_id");
    }

    public function scopeActiveJobs($query): Builder
    {
        return $query->where('active', '=', '1');
    }

    public function scopeCurrentUser($query): Builder
    {
        return $query->where('user_id', '=', auth()->id());
    }
}
