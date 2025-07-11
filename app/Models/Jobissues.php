<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;

class Jobissues extends Model
{
    protected $table = 'codeanalyzer_job_issues';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    public function job()
    {
        return $this->belongsTo(Jobs::class);
    }

    public function cropText()
    {
        if(strlen($this->text) > 20)
        {
            return substr($this->text,0, 50)."...";
        }
        return $this->text;
    }
    public function scopeCurrentUser($query): Builder
    {
        return $query->where('user_id', '=', auth()->id());
    }
}
