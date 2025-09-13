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
        try {
            Mail::send(new MailSent($this->mail));
            $this->deleteAttachmentsFromStorage($this->mail->getAttachments());

            // Update is_sent flag to true after sending the email
            $this->mail->setIsSent(true);
            $this->mail->save();

            Log::info($this->mail->getId().' Email sent at '.date('Y-m-d H:i:s'));
        } catch (\Exception $e) {
            // Log the error
            Log::error($this->mail->getId().' Email sending failed: '.$e->getMessage());

            // You could add a failure_reason field to the Mail model and update it here
            // $this->mail->setFailureReason($e->getMessage());
            // $this->mail->save();

            // Re-throw the exception if you want the job to be retried
            throw $e;
        }
    }

    /**
     * Determine number of times the job may be attempted.
     */
    public function tries(): int
    {
        return 3;
    }

    private function deleteAttachmentsFromStorage(string $attachments): void
    {
        $attachments = json_decode($attachments);

        $files = array_map(fn (stdClass $attachment) => $attachment->attachFileName, $attachments);
        Storage::disk('attachments')->delete($files);
    }
}
