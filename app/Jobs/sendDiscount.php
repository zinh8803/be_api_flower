<?php

namespace App\Jobs;

use App\Mail\DiscountMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Bus\Dispatchable;

class sendDiscount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $discount;
    public $email;
    public function __construct($discount, $email)
    {
        $this->discount = $discount;
        $this->email = $email;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->email)->send(new DiscountMail($this->discount));
        } catch (\Throwable $e) {
            Log::error('Error sending discount report email: ' . $e->getMessage());
        }
    }
}
