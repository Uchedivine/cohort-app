<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\FileUploadService;
use App\Services\ContentRevisionService;
use App\Services\SubmissionService;
use App\Services\NotificationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register services as singletons
        $this->app->singleton(FileUploadService::class);
        $this->app->singleton(ContentRevisionService::class);
        $this->app->singleton(SubmissionService::class);
        $this->app->singleton(NotificationService::class);
        $this->app->singleton(\App\Services\JobMonitorService::class);
        $this->app->singleton(\App\Services\SearchService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
