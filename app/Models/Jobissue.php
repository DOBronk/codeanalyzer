<?php

namespace App\Models;

use App\Traits\UserScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jobissue extends Model
{
    use UserScope, HasFactory;

    protected $table = 'codeanalyzer_job_issues';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['job_id', 'jobitem_id', 'user_id', 'title', 'text', 'git_url'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobItem(): BelongsTo
    {
        return $this->belongsTo(Jobitem::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
