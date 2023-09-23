<?php

namespace App\Jobs;

use App\Mail\MailSent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use stdClass;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public \App\Models\Mail $mail)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::send(new MailSent($this->mail));
        $this->deleteAttachmentsFromStorage($this->mail->getAttachments());
        Log::info($this->mail->getId().' Email sent at '.date('Y-m-d H:i:s'));
    }

    private function deleteAttachmentsFromStorage(string $attachments): void
    {
        $attachments = json_decode($attachments);

        $files = array_map(fn (stdClass $attachment) => $attachment->attachFileName, $attachments);
        Storage::disk('attachments')->delete($files);
    }
}
