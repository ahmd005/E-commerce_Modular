<?php

namespace Modules\Notifications\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Notifications\Reposiotires\Contracts\NotificationRepositoryInterface;
use Modules\Notifications\Reposiotires\NotificationRepository;

class NotificationRepositoryServiceProvider extends ServiceProvider
{
    /**
     * تسجيل الـ bindings في الحاوية.
     */
    public function register(): void
    {
        $this->app->bind(
            NotificationRepositoryInterface::class,
            NotificationRepository::class,
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}