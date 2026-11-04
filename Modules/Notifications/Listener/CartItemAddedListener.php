<?php

namespace Modules\Notifications\Listeners;

use App\Events\ItemAddedToCart;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Notifications\Actions\NotificationsAction;

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
            type: 'cart_item_added',
            notifiable: $cartItem,
            data: [
                'cart_item_id' => $cartItem->id,
                'product_id'   => $cartItem->product_id ?? null,
            ],
        );
    }
}