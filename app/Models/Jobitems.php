<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jobitems extends Model
{
    protected $fillable = ['job_id', 'path', 'blob_sha', 'status_id', 'results'];
    protected $casts = ['results' => 'array'];
    protected $table = 'codeanalyzer_job_items';
    protected $with = ['status'];

    public function status()
    {
        return $this->belongsTo(Jobstatus::class);
    }

    public function job()
    {
        return $this->belongsTo(Jobs::class);
    }

}
