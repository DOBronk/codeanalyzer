<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JobStep1Controller;
use App\Http\Controllers\JobStep2Controller;
use App\Http\Controllers\CancleJobController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\SettingsController;
use App\Models\Jobs;
use App\Models\Jobissues;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['can:noActiveJobs,App\Models\Jobs', 'can:hasAPI,App\Models\User'])->group(function (): void {
        // Create job step 1
        Route::get('/create-1', [JobStep1Controller::class, 'index'])->name('codeanalyzer.create.step.one');
        Route::post('/create-1', [JobStep1Controller::class, 'create'])->name('codeanalyzer.create.step.one.post');
        // Create job step 2
        Route::get('/create-2', [JobStep2Controller::class, 'index'])->name('codeanalyzer.create.step.two');
        Route::post('/create-2', [JobStep2Controller::class, 'store'])->name('codeanalyzer.create.step.two.post');
    });

    Route::get("/job/cancel/{jobs}", [CancleJobController::class, 'index'])->name('job.cancel');

    // Job's items/details overview
    Route::get('/job/{jobs}', fn(Jobs $jobs) => view('jobs.jobdetails', ['job' => $jobs]))
        ->middleware('can:view,jobs')
        ->name('codeanalyzer.job');
    Route::get('/', fn() => view('jobs.index', ['items' => Jobs::with('items')->orderByDesc('created_at')->currentUser()->paginate(10)]))->name('codeanalyzer.index');

    // Create issue
    Route::middleware(['can:create,jobitems', 'can:hasAPI,App\Models\User'])->group(function (): void {
        Route::get('/issues/create/{jobitems}', [IssueController::class, 'create'])->name('codeanalyzer.createissue');
        Route::post('/issues/create/{jobitems}', [IssueController::class, 'store'])->name('codeanalyzer.storeissue');
    });

    route::get('/issues', [IssueController::class, 'index'])->name('codeanalyzer.issues');
    route::get('/issues/{jobissues}', [IssueController::class, 'show'])
        ->middleware('can:view,jobissues')
        ->name('codeanalyzer.showissue');

    route::get('/settings', [SettingsController::class, 'index'])->name('codeanalyzer.settings');
    route::post('/settings', [SettingsController::class, 'store'])->name('codeanalyzer.postsettings');
});

require __DIR__ . '/auth.php';
