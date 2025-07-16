<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIssueRequest;
use App\Models\Jobissue;
use App\Models\Jobitem;
use App\Services\GithubService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class IssueController extends Controller
{
    /**
     * Index page for displaying issues
     */
    public function index(): View
    {
        return view('issues.index', ['items' => Jobissue::with('job')->orderByDesc('created_at')->currentUser()->paginate(10)]);
    }

    public function show(Jobissue $jobissue): View
    {
        return view('issues.show', ['item' => $jobissue, 'job' => $jobissue->job]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Jobitem $jobitem): View
    {
        if ($jobitem->status_id != 1) {
            abort(422, 'Onjuiste issue status');
        }

        return view('issues.create', ['item' => $jobitem]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIssueRequest $request, Jobitem $jobitem, GithubService $git): RedirectResponse
    {
        try {
            $link = $git->createIssue($jobitem->job->owner, $jobitem->job->repository, $request['title'], $request['text']);
            Jobissue::create(['git_url' => $link, ...$request->validated()]);
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }

        $jobitem->update(['status_id' => 3]);

        return redirect()->route('codeanalyzer.job', ['jobs' => $jobitem->job])->with('message', 'Issue succesvol aangemaakt');
    }
}
