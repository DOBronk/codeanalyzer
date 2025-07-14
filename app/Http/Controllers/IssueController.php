<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIssueRequest;
use App\Models\Jobitems;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Jobissues;
use App\Services\GithubService;

class IssueController extends Controller
{
    /**
     * Index page for displaying issues
     */
    public function index(): View
    {
        return view('issues.index', ['items' => Jobissues::with('job')->orderByDesc('created_at')->currentUser()->paginate(10)]);
    }

    public function show(Jobissues $jobissues): View
    {
        return view('issues.show', ['item' => $jobissues, 'job' => $jobissues->job]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(Jobitems $jobitems): View
    {
        if ($jobitems->status_id != 1) {
            abort(422, 'Onjuiste issue status');
        }
        return view('issues.create', ['item' => $jobitems]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIssueRequest $request, Jobitems $jobitems,  GithubService $git): RedirectResponse
    {
        try {
            $link = $git->createIssue($jobitems->job->owner, $jobitems->job->repository, $request['title'], $request['text']);
            Jobissues::create(['git_url' => $link, ...$request->validated()]);
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }

        $jobitems->update(['status_id' => 3]);

        return redirect()->route('codeanalyzer.job', ['jobs' => $jobitems->job])->with('message', 'Issue succesvol aangemaakt');
    }
}
