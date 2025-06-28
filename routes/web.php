<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CodeAnalyzerController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\SettingsController;
use App\Models\Jobs;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('can:noActiveJobs,App\Models\Jobs')->group(function (): void {
        // Create job step 1
        Route::get('/create-1', [CodeAnalyzerController::class, 'createStepOne'])->name('codeanalyzer.create.step.one');
        Route::post('/create-1', [CodeAnalyzerController::class, 'postCreateStepOne'])->name('codeanalyzer.create.step.one.post');
        // Create job step 2
        Route::get('/create-2', [CodeAnalyzerController::class, 'createStepTwo'])->name('codeanalyzer.create.step.two');
        Route::post('/create-2', [CodeAnalyzerController::class, 'postCreateStepTwo'])->name('codeanalyzer.create.step.two.post');
    });

    // Job's items/details overview
    Route::get(
        '/job/{jobs}',
        function (Jobs $jobs) {
            return view('jobs.jobdetails', ['job' => $jobs]);
        }
    )->name('codeanalyzer.job');

    Route::get('/', function () {
        return view('jobs.index', ['items' => Jobs::query()->currentUser()->with('items')->get()]);
    })->name('codeanalyzer.index');

    // Create issue
    Route::get('/issues/create/{id}', [IssueController::class, 'create'])->name('codeanalyzer.createissue');
    Route::post('/issues/create/{id}', [IssueController::class, 'store'])->name('codeanalyzer.storeissue');

    route::get('/issues', [IssueController::class, 'index'])->name('codeanalyzer.issues');
    route::get('/issues/{id}', [IssueController::class, 'show'])->name('codeanalyzer.showissue');

    route::get('/settings', [SettingsController::class, 'index'])->name('codeanalyzer.settings');
    route::post('/settings', [SettingsController::class, 'store'])->name('codeanalyzer.settings');
});


require __DIR__ . '/auth.php';
