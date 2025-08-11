<?php

use App\Http\Controllers\CancleJobController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\JobStep1Controller;
use App\Http\Controllers\JobStep2Controller;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RepositoriesController;
use App\Http\Controllers\SettingsController;
use App\Http\Middleware\MandatorySessionKeys;
use App\Models\Job;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\FileManagerController;

Route::get("/test", fn() => "Simpel")->name('simpel');
Route::get("/redirect", fn() => response()->json(['redirect' => url(route('simpel'))]));

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['can:noActiveJobs,App\Models\Job', 'can:hasAPI,App\Models\User'])->group(function (): void {
        // Create job step 1
        Route::get('/create-1', [JobStep1Controller::class, 'index'])->name('codeanalyzer.create.step.one');
        Route::post('/create-1', [JobStep1Controller::class, 'create'])->name('codeanalyzer.create.step.one.post');
        // Create job step 2
        Route::get('/create-2', [JobStep2Controller::class, 'index'])->name('codeanalyzer.create.step.two')
            ->middleware(MandatorySessionKeys::class);
        Route::post('/create-2', [JobStep2Controller::class, 'store'])->name('codeanalyzer.create.step.two.post');
        //  ->middleware(MandatorySessionKeys::class);
    });

    Route::get('/job/cancel/{job}', [CancleJobController::class, 'index'])->name('job.cancel');

    // Job's items/details overview
    Route::get('/job/{job}', fn(Job $job) => view('jobs.jobdetails', ['job' => $job]))
        ->middleware('can:view,job')
        ->name('codeanalyzer.job');
    Route::get('/', fn() => view('jobs.index', ['items' => Job::with('items')->orderByDesc('created_at')->currentUser()->paginate(10)]))->name('codeanalyzer.index');
    Route::get('/te', function () {
        return Job::first()->load(relations: ['items'])->user->settings->gh_api_key;
    });

    // Create issue
    Route::middleware(['can:create,jobitem', 'can:hasAPI,App\Models\User'])->group(function (): void {
        Route::get('/issues/create/{jobitem}', [IssueController::class, 'create'])->name('codeanalyzer.createissue');
        Route::post('/issues/create/{jobitem}', [IssueController::class, 'store'])->name('codeanalyzer.storeissue');
    });

    route::get('/issues', [IssueController::class, 'index'])->name(name: 'codeanalyzer.issues');
    route::get('/issues/{jobissue}', [IssueController::class, 'show'])
        ->middleware('can:view,jobissue')
        ->name('codeanalyzer.showissue');

    route::get('/settings', [SettingsController::class, 'index'])->name('codeanalyzer.settings');
    route::post('/settings', [SettingsController::class, 'store'])->name('codeanalyzer.postsettings');
    Route::post('/data', function (Request $request) {
        return [[
            'key' => '12',
            'data' => [
                'name' => 'nieuwe naam',
                'type' => $request['antwoord'],
                'size' => '2.0012 PB'
            ]
        ]];
    });
    Route::post('/gettree', [RepositoriesController::class, 'show']);
    Route::post('/getdefault', [RepositoriesController::class, 'edit']);
    Route::post('/getrepositories', [RepositoriesController::class, 'index']);
    Route::post('/getbranches', [RepositoriesController::class, 'store']);
    Route::post('/showdata', [RepositoriesController::class, 'create']);
    Route::get('inert', [FileManagerController::class, 'index2']);
});

Route::get('/data2', function (Request $request) {
    return [[
        'key' => '12',
        'data' => [
            'name' => 'nieuwe naam',
            'type' => 'test',
            'size' => '2.0012 PB'
        ]
    ]];
});
Route::get('in', [FileManagerController::class, 'index']);
Route::post('/tester', function (Request $t) {
    Log::info("Post data kk webhook", ['data' => $t->selections, 'twee' => $t->getContent()]);
    return 'zooi';
});

require __DIR__ . '/auth.php';
