<?php

namespace App\Jobs;

use App\Mail\OrderStatusUpdatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOrderStatusMailJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    protected $statusText;
    /**
     * Create a new job instance.
     */
    public function __construct($order, $statusText)
    {
        $this->order = $order;
        $this->statusText = $statusText;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->order->email)
            ->send(new OrderStatusUpdatedMail($this->order, $this->statusText));
    }
}
