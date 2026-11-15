<?php

namespace Modules\Orders\App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Orders\App\Mail\OrderCreatedMail;
use Modules\Orders\Models\Order;

class SendOrderConfirmationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Order $order;

    // إعادة المحاولة 3 مرات في حال فشل الاتصال بالـ SMTP
    public int $tries = 3;

   //  2. الانتظار بالثواني بين كل محاولة ومحاولة (Backoff)
    public int $backoff = 10;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function handle(): void
    {
       
        // throw new \Exception("تجربة فشل الجوب واختبار الـ Dead Letter Queue");

        $this->order->loadMissing(['user', 'items.product']);

        if ($this->order->user && $this->order->user->email) {
            Mail::to($this->order->user->email)->send(new OrderCreatedMail($this->order));
        }
    }

    public function failed(Exception $exception): void
    {
        Log::error("DLQ - فشل نهائي لإرسال إيميل إنشاء الطلب #{$this->order->uuid}: " . $exception->getMessage());
    }
}