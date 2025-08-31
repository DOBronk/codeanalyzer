<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Services\GitHubApi\GithubService;
use App\Services\RabbitMqBroker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Gate::define('hasAPI', function (User $user) {
            return $user->settings->gh_api_key != null;
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
        Model::preventLazyLoading(true);
        Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
            report(new \Exception("lazy loaded $model on relation $relation"));
            abort(404);
        });
    }
}
