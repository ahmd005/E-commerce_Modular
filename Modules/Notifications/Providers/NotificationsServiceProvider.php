<?php

namespace Modules\Notifications\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Modules\Notifications\Providers\NotificationRepositoryServiceProvider;
class NotificationsServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Notifications';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'notifications';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    // protected array $commands = [];

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    /**
     * Define module schedules.
     * 
     * @param $schedule
     */
    // protected function configureSchedules(Schedule $schedule): void
    // {
    //     $schedule->command('inspire')->hourly();
    // }
    public function register(): void
{
    $this->app->register(EventServiceProvider::class);
        $this->app->register(NotificationRepositoryServiceProvider::class);

}
public function boot(): void
    {
        // تحميل routes الموديول
        $this->loadRoutesFrom(base_path('Modules/Notifications/routes/api.php'));
    }
}
