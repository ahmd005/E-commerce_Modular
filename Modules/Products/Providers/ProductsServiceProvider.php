<?php

namespace Modules\Products\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Modules\Products\Repositories\ProductRepositoryInterface;
use Modules\Products\Repositories\ProductRepository;
use Modules\Products\Repositories\CategoryRepositoryInterface;
use Modules\Products\Repositories\CategoryRepository;

class ProductsServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Products';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'products';

    /**
     * Register bindings in the container.
     */
    public function register(): void
    {
        parent::register();

        // ربط الـ Interface مع الـ Repository الخاص بالمنتجات
        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );

        $this->app->bind(
            CategoryRepositoryInterface::class,
            CategoryRepository::class
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
