<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = 'user_settings';
    protected $fillable = ['user_id','gh_api_key'];
}
