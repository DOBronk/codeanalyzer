<?php

namespace App\Http\Controllers;

use App\Models\Jobs;
use Illuminate\Http\Request;

class CancleJobController extends Controller
{
    public function index(Jobs $jobs)
    {
        $jobs->active = 0;

        $jobs->save();

        return  back()->with('message', 'Job geannuleerd');
    }
}
