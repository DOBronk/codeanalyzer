<?php

namespace App\Http\Controllers;

use App\Models\Job;

class CancleJobController extends Controller
{
    public function index(Job $job)
    {
        $job->active = 0;

        $job->save();

        return back()->with('message', 'Job geannuleerd');
    }
}
