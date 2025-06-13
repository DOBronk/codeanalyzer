<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Policies\CreateJobPolicy;
use Illuminate\Support\Facades\Gate;
use App\Services\GithubService;
use App\Services\RabbitMqBroker;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Gate::define('noActiveJobs', [CreateJobPolicy::class, 'noActiveJobs']);

        $this->app->bind("App\Services\GithubService", function () {
            return new GithubService(config('codeanalyzer.gh_uri'), config('codeanalyzer.gh_key'));
        });

        $this->app->bind("App\Services\MessageBroker", function () {
            return new RabbitMqBroker(
                config('codeanalyzer.rabbitmq_host'),
                config('codeanalyzer.rabbitmq_port'),
                config('codeanalyzer.rabbitmq_username'),
                config('codeanalyzer.rabbitmq_password'),
                config('codeanalyzer.rabbitmq_queue')
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
