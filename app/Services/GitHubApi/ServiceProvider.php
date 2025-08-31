<?php

namespace App\Services\GitHubApi;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Support\Facades\Http;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        Http::macro('github', fn() => Http::withHeaders([
            'Accept' => 'application/vnd.github+json',
            'X-GitHub-Api-Version' => '2022-11-28',
            'User-Agent' => 'Codeanalyzer (gh_user: DOBronk)'
        ])->baseUrl(config('codeanalyzer.gh_uri')));
    }

    public function register()
    {
        $this->app->bind("App\Services\GitHubApi\GithubService", function () {
            if (Auth::check()) {
                $key = Auth::user()->settings->gh_api_key;
            }

            return new GithubService(config('codeanalyzer.gh_uri'), $key ?? '');
        });
    }
}
