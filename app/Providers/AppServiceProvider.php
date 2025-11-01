<?php

namespace App\Providers;

use App\Models\Attempt;
use App\Models\FeedbackReflection;
use App\Observers\AttemptObserver;
use App\Observers\FeedbackReflectionObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Attempt::observe(AttemptObserver::class);
        FeedbackReflection::observe(FeedbackReflectionObserver::class);
    }
}
