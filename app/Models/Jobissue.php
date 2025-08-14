<?php

namespace App\Models;

use App\Traits\UserScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $job_id
 * @property int $jobitem_id
 * @property int $user_id
 * @property string $title
 * @property string $text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $git_url
 * @property-read \App\Models\Job $job
 * @property-read \App\Models\Jobitem|null $jobItem
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobissue currentUser()
 * @method static \Database\Factories\JobissueFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobissue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobissue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobissue query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobissue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobissue whereGitUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobissue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobissue whereJobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobissue whereJobitemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobissue whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobissue whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobissue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobissue whereUserId($value)
 * @mixin \Eloquent
 */
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
