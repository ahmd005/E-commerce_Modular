<?php

namespace Modules\Orders\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Modules\Orders\Repositories\CartRepositoryInterface;
use Modules\Orders\Repositories\CartRepository;
use Modules\Orders\Repositories\OrderRepositoryInterface;
use Modules\Orders\Repositories\OrderRepository;

class OrdersServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Orders';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'orders';

    /**
     * Register bindings in the container.
     */
    public function register(): void
    {
        parent::register();

        // ربط الـ Interface مع الـ Repository الخاص بالسلة
        $this->app->bind(
            CartRepositoryInterface::class,
            CartRepository::class
        );

        // ربط الـ Interface مع الـ Repository الخاص بالطلبات
        $this->app->bind(
            OrderRepositoryInterface::class,
            OrderRepository::class
        );
    }

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
}
