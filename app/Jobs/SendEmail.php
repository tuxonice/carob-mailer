<?php

namespace App\Jobs;

use App\Models\Mail;
use App\Services\MailDeliveryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Mail $mail)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(MailDeliveryService $mailDeliveryService): void
    {
        $mailDeliveryService->send($this->mail);
    }

    /**
     * Determine number of times the job may be attempted.
     */
    public function tries(): int
    {
        return 3;
    }
}
