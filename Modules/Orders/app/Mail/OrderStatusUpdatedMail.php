<?php

namespace Modules\Orders\App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Orders\Models\Order;

class OrderStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public string $newStatus;

    public function __construct(Order $order, string $newStatus)
    {
        $this->order = $order;
        $this->newStatus = $newStatus;
    }

    public function build()
    {
        return $this->subject('تحديث حالة طلبك #' . $this->order->id)
                    ->view('orders::emails.order_status_updated');
    }
}