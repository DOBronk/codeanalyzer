<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobstatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobstatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobstatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobstatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobstatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobstatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jobstatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Jobstatus extends Model
{
    protected $fillable = ['id', 'name'];

    protected $table = 'codeanalyzer_job_status';
}
