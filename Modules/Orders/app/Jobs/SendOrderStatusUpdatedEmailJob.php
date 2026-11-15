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
use Modules\Orders\App\Mail\OrderStatusUpdatedMail;
use Modules\Orders\Models\Order;

class SendOrderStatusUpdatedEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Order $order;
    public string $newStatus;

    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function __construct(Order $order, string $newStatus)
    {
        $this->order = $order;
        $this->newStatus = $newStatus;
    }

    public function handle(): void
    {
        $this->order->loadMissing('user');

        if ($this->order->user && $this->order->user->email) {
            Mail::to($this->order->user->email)->send(new OrderStatusUpdatedMail($this->order, $this->newStatus));
        }
    }
/**
     * التعامل مع الفشل الذريع بعد استنفاد كل المحاولات (تحويلها إلى Dead Letter / Failed Jobs)
     */
    public function failed(Exception $exception): void
    {
        Log::error("DLQ - فشل نهائي لإرسال إيميل تحديث الحالة للطلب #{$this->order->uuid}: " . $exception->getMessage());
    }
}