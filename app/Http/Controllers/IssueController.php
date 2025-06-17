<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Jobitems;
use App\Models\Jobs;
use App\Models\Jobissues;
use App\Services\GithubService;
class IssueController extends Controller
{
    public function index()
    {
        $issues = Jobissues::query()->currentUser()->with('job')->get();

        return view('issues.index', ['items' => $issues]);
    }

    public function show($id)
    {
        $issue = Jobissues::find($id);

        if(empty($issue)) {
            abort(404);
        }

        return view('issues.show',['item' => $issue]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $item = Jobitems::find($id);

        if(empty($item)) {
            abort(404);
        } elseif($item->status_id != 1) {
            abort(500, 'Onjuiste issue status');
        }
        return view('issues.create', ['item' => $item]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($id, Request $request, GithubService $git)
    {
        $item = Jobitems::find($id);

        if(empty($item)) {
            abort(404);
        }

        $data = $request->validate([ 'issuetext' => 'required|string',
                                            'title' => 'required|string|max:1024' ]);
        
        $job = Jobs::find($item->job_id);
        DB::beginTransaction();

        try{
            $issue = new Jobissues();
            $issue->job_id = $job->id;
            $issue->jobitem_id = $item->id;
            $issue->user_id = $request->user()->id;
            $issue->title = $data['title'];
            $issue->text = $data['issuetext'];
            $issue->save();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withError("Kon issue record niet aanmaken!");
        }

        $item->status_id = 3;
        $item->save();

        try{
            $issue->git_url = $git->createIssue($job->owner,$job->repo,$data['title'],$data['issuetext'], apikey: $request->user()->settings->gh_api_key);
            $issue->save();
        }
        catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withError($e->getMessage());
        }

        DB::commit();
        return redirect()->route('codeanalyzer.job', ['id' => $job])->with('message','Issue succesvol aangemaakt');
    }
}
