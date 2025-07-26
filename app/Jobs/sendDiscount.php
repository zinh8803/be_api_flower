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
    public $discounts;
    public $email;
    public function __construct($discounts, $email)
    {
        $this->discounts = is_array($discounts) || $discounts instanceof \Illuminate\Database\Eloquent\Collection
            ? $discounts
            : [$discounts];
        $this->email = $email;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            if (count($this->discounts) > 0) {
                Mail::to($this->email)->send(new DiscountMail($this->discounts));
            }
        } catch (\Throwable $e) {
            Log::error('Error sending discount email: ' . $e->getMessage());
        }
    }
}
