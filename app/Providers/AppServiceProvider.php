<?php

namespace App\Providers;

use App\Contracts\AnswerServiceInterface;
use App\Contracts\QuestionServiceInterface;
use App\Contracts\ReputationServiceInterface;
use App\Models\Answer;
use App\Policies\AnswerPolicy;
use App\Services\AnswerService;
use App\Services\QuestionService;
use App\Services\ReputationService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ReputationServiceInterface::class, ReputationService::class);
        $this->app->bind(QuestionServiceInterface::class, QuestionService::class);
        $this->app->bind(AnswerServiceInterface::class, AnswerService::class);
    }

    public function boot(): void
    {
        Gate::policy(Answer::class, AnswerPolicy::class);
    }
}