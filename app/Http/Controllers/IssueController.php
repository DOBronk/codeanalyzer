<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jobitems;
use App\Models\Jobs;
use App\Models\Jobissues;
use App\Services\GithubService;

class IssueController extends Controller
{
    public function index(GithubService $git)
    {
        return view('issues.index', ['items' => Jobissues::with('job')->orderByDesc('created_at')->currentUser()->paginate(10)]);
    }

    public function show(Jobissues $jobissues)
    {
        return view('issues.show', ['item' => $jobissues, 'job' => $jobissues->job]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(Jobitems $jobitems)
    {
        if ($jobitems->status_id != 1) {
            abort(500, 'Onjuiste issue status');
        }
        return view('issues.create', ['item' => $jobitems]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Jobitems $jobitems, Request $request, GithubService $git)
    {
        extract($request->validate([
            'issuetext' => 'required|string',
            'title' => 'required|string|max:1024'
        ]));

        $job = Jobs::find($jobitems->job_id);

        try {
            Jobissues::create([
                'job_id' => $job->id,
                'jobitem_id' => $jobitems->id,
                'user_id' => $request->user()->id,
                'title' => $title,
                'text' => $issuetext,
                'git_url' => $git->createIssue($job->owner, $job->repo, $title, $issuetext),
            ]);
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }

        $jobitems->update(['status_id' => 3]);

        return redirect()->route('codeanalyzer.job', ['jobs' => $job])->with('message', 'Issue succesvol aangemaakt');
    }
}
