<?php

namespace Modules\Notifications\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\OrderCreated;
use App\Events\OrderStatusChanged;
use App\Events\ItemAddedToCart;
use Modules\Notifications\Listeners\SendOrderCreatedNotification;
use Modules\Notifications\Listeners\SendOrderStatusChangedNotification;
use Modules\Notifications\Listeners\SendCartItemAddedNotification;
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {}
    protected $listen = [
        OrderCreated::class => [
            SendOrderCreatedNotification::class,
        ],
        OrderStatusChanged::class => [
            SendOrderStatusChangedNotification::class,
        ],
        ItemAddedToCart::class => [
            SendCartItemAddedNotification::class,
        ],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false; // نعرّفها صراحة
    }
}
