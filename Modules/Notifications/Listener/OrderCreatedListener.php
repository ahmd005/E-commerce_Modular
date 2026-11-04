<?php

namespace Modules\Notifications\Listeners;

use App\Events\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Notifications\Actions\NotificationsAction;

class SendOrderCreatedNotification implements ShouldQueue
{
    public $queue = 'notifications';

    public function __construct(
        private NotificationsAction $notifications
    ) {}

    public function handle(OrderCreated $event): void
    {
        $order = $event->order;

        $this->notifications->createForUser(
            user: $order->user,
            title: 'تم إنشاء طلبك',
            message: "تم إنشاء طلبك رقم {$order->id} بنجاح.",
           
        );
    }
}