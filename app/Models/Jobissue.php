<?php

namespace App\Models;

use App\Traits\UserScope;
use Illuminate\Database\Eloquent\Model;

class Jobissue extends Model
{
    use UserScope;

    protected $table = 'codeanalyzer_job_issues';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['job_id', 'jobitem_id', 'user_id', 'title', 'text', 'git_url'];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
