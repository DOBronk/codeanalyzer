<?php

namespace App\Models;

use App\Utilities\Results;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Jobitem extends Model
{
    protected $fillable = ['job_id', 'path', 'sha', 'status_id', 'results'];

    protected $table = 'codeanalyzer_job_items';

    protected $with = ['status'];

    protected $casts = ['results' => 'array'];

    public function status()
    {
        return $this->belongsTo(Jobstatus::class);
    }

    public function issues()
    {
        return $this->hasMany(Jobissue::class, 'jobitem_id');
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    protected function filteredResults(): Attribute
    {
        return Attribute::make(
            get: fn (): array => Results::hasImprovements($this->results),
        )->shouldCache();
    }

    public function resultsToString(): string
    {
        return Cache::remember('results_string'.$this->id, (6800), function () {
            return Results::resultsToString($this->filteredResults);
        });
    }
}
