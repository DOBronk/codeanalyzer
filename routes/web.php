<?php

use App\Http\Controllers\CreateJobController;
use App\Http\Controllers\GitResources\GetBranchesController;
use App\Http\Controllers\GitResources\GetRepositoriesController;
use App\Http\Controllers\GitResources\GetRepositoryTreeController;
use App\Http\Controllers\CancleJobController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Models\Job;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['can:noActiveJobs,App\Models\Job', 'can:hasAPI,App\Models\User'])->group(function (): void {
        // Create job with Vue Component loaded through Inertia and Blade
        Route::get('/createjob', [CreateJobController::class, 'index'])->name('codeanalyzer.createjob');
        Route::post('/createjob', [CreateJobController::class, 'store'])->name('codeanalyzer.createjob.post');
    });

    Route::get('/job/cancel/{job}', [CancleJobController::class, 'index'])->name('job.cancel');

    // Job's items/details overview
    Route::get('/job/{job}', fn(Job $job) => view('jobs.jobdetails', ['job' => $job]))
        ->middleware('can:view,job')
        ->name('codeanalyzer.job');
    Route::get('/', fn() => view('jobs.index', ['items' => Job::with('items')->orderByDesc('created_at')->currentUser()->paginate(10)]))->name('codeanalyzer.index');

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

    // AJAX routes for Vue Repository Treeviewer component
    Route::post('/gettree', GetRepositoryTreeController::class)->name('ajax.gettree');
    Route::post('/getrepositories', GetRepositoriesController::class)->name('ajax.getrepositories');
    Route::post('/getbranches', GetBranchesController::class)->name('ajax.getbranches');
});

require __DIR__ . '/auth.php';
