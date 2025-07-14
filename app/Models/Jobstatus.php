<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jobstatus extends Model
{
    protected $fillable = ['id', 'name'];

    protected $table = 'codeanalyzer_job_status';
}
