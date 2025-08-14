<?php

namespace App\Models;

use App\Utilities\Results;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property int $job_id
 * @property string $path
 * @property string $sha
 * @property int $status_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property array<array-key, mixed>|null $results
 * @property int|null $issue_id
 * @property-read array $filtered_results
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Jobissue> $issues
 * @property-read int|null $issues_count
 * @property-read \App\Models\Job $job
 * @property-read \App\Models\Jobstatus|null $status
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\JobitemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobitem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobitem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobitem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobitem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobitem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobitem whereIssueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobitem whereJobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobitem wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobitem whereResults($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobitem whereSha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobitem whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobitem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Jobitem extends Model
{
    use HasFactory;
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    protected function filteredResults(): Attribute
    {
        return Attribute::make(
            get: fn(): array => Results::hasImprovements($this->results),
        )->shouldCache();
    }

    public function resultsToString(): string
    {
        return Cache::remember('results_string' . $this->id, (6800), function () {
            return Results::resultsToString($this->filteredResults);
        });
    }
}
