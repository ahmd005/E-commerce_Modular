<?php

namespace Modules\Notifications\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Notifications\Actions\NotificationsAction;
use Modules\Orders\Events\ItemAddedToCart;
class SendCartItemAddedNotification implements ShouldQueue
{
    public $queue = 'notifications';

    public function __construct(
        private NotificationsAction $notifications
    ) {}

    public function handle(ItemAddedToCart $event): void
    {
        $cartItem = $event->cart;

        // مثال: أشعر المستخدم عند إضافة منتج (اختياري حسب سياستك)
        $this->notifications->createForUser(
            user: $cartItem->cart->user,   // عدّل حسب structure الحقيقي
            title: 'تمت إضافة منتج إلى السلة',
            message: 'تمت إضافة منتج جديد إلى سلة مشترياتك.',
            
        );
    }
}