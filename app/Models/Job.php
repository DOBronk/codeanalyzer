<?php

namespace App\Models;

use App\Traits\UserScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $user_id
 * @property string $owner
 * @property string $repository
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $branch
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Jobitem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\User $user
 * @method static Builder<static>|Job activeJobs()
 * @method static Builder<static>|Job currentUser()
 * @method static \Database\Factories\JobFactory factory($count = null, $state = [])
 * @method static Builder<static>|Job newModelQuery()
 * @method static Builder<static>|Job newQuery()
 * @method static Builder<static>|Job query()
 * @method static Builder<static>|Job whereActive($value)
 * @method static Builder<static>|Job whereBranch($value)
 * @method static Builder<static>|Job whereCreatedAt($value)
 * @method static Builder<static>|Job whereId($value)
 * @method static Builder<static>|Job whereOwner($value)
 * @method static Builder<static>|Job whereRepository($value)
 * @method static Builder<static>|Job whereUpdatedAt($value)
 * @method static Builder<static>|Job whereUserId($value)
 * @mixin \Eloquent
 */
class Job extends Model
{
    use UserScope, HasFactory;

    protected $fillable = ['user_id', 'owner', 'repository', 'active', 'branch'];

    protected $table = 'codeanalyzer_jobs';

    public function items()
    {
        return $this->hasMany(Jobitem::class, 'job_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActiveJobs(Builder $query): Builder
    {
        return $query->where('active', '=', '1');
    }
}
