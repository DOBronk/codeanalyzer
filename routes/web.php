<?php

use App\Http\Controllers\CancleJobController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\JobStep1Controller;
use App\Http\Controllers\JobStep2Controller;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Models\Job;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['can:noActiveJobs,App\Models\Job', 'can:hasAPI,App\Models\User'])->group(function (): void {
        // Create job step 1
        Route::get('/create-1', [JobStep1Controller::class, 'index'])->name('codeanalyzer.create.step.one');
        Route::post('/create-1', [JobStep1Controller::class, 'create'])->name('codeanalyzer.create.step.one.post');
        // Create job step 2
        Route::get('/create-2', [JobStep2Controller::class, 'index'])->name('codeanalyzer.create.step.two');
        Route::post('/create-2', [JobStep2Controller::class, 'store'])->name('codeanalyzer.create.step.two.post');
    });

    Route::get('/job/cancel/{job}', [CancleJobController::class, 'index'])->name('job.cancel');

    // Job's items/details overview
    Route::get('/job/{job}', fn (Job $job) => view('jobs.jobdetails', ['job' => $job]))
        ->middleware('can:view,job')
        ->name('codeanalyzer.job');
    Route::get('/', fn () => view('jobs.index', ['items' => Job::with('items')->orderByDesc('created_at')->currentUser()->paginate(10)]))->name('codeanalyzer.index');

    // Create issue
    Route::middleware(['can:create,jobitem', 'can:hasAPI,App\Models\User'])->group(function (): void {
        Route::get('/issues/create/{jobitem}', [IssueController::class, 'create'])->name('codeanalyzer.createissue');
        Route::post('/issues/create/{jobitem}', [IssueController::class, 'store'])->name('codeanalyzer.storeissue');
    });

    route::get('/issues', [IssueController::class, 'index'])->name('codeanalyzer.issues');
    route::get('/issues/{jobissue}', [IssueController::class, 'show'])
        ->middleware('can:view,jobissue')
        ->name('codeanalyzer.showissue');

    route::get('/settings', [SettingsController::class, 'index'])->name('codeanalyzer.settings');
    route::post('/settings', [SettingsController::class, 'store'])->name('codeanalyzer.postsettings');
});

require __DIR__.'/auth.php';
