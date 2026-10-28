<?php

namespace Modules\Users\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Modules\Users\Repositries\Contracts\UserRepositoryInterface;
use Modules\Users\Repositries\UserRepository;
use Modules\Users\app\Contracts\UserContract;
use Modules\Users\app\Infrastructure\UserService;
class UsersServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Users';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'users';

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
    // ضع هنا الـ Bindings (مثل الـ Repositories والـ Contracts)
    $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserContract::class, UserService::class);

}
}
