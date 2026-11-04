<?php

namespace Modules\Notifications\Listeners;

use Modules\Orders\Events\OrderStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Notifications\Actions\NotificationsAction;

class SendOrderStatusChangedNotification implements ShouldQueue
{
    public $queue = 'notifications';

    public function __construct(
        private NotificationsAction $notifications
    ) {}

    public function handle(OrderStatusChanged $event): void
    {
        $order = $event->order;

        // لا تُشعر المستخدم بنفس الحالة مرتين
        if ($order->status === $event->newStatus) {
            return;
        }

        $this->notifications->createForUser(
            user: $order->user,
            title: 'تحديث حالة الطلب',
            message: "حالة طلبك رقم {$order->id} تغيّرت إلى: {$event->newStatus}",
        );
    }
}