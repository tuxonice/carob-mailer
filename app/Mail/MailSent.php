<?php

namespace App\Mail;

use App\Models\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class MailSent extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Mail $mail
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('app.mail_from_address'), $this->mail->getFromName()),
            to: [new Address($this->mail->getToEmail(), $this->mail->getToName())],
            subject: $this->mail->getSubject(),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'emails.html',
            text: 'emails.text',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = json_decode($this->mail->getAttachments(), true) ?? [];

        $files = [];
        foreach ($attachments as $attachment) {
            $resource = Storage::disk('attachments')->readStream($attachment['attachFileName']);
            $mimeContentType = mime_content_type($resource);

            $files[] = Attachment::fromStorageDisk('attachments', $attachment['attachFileName'])
                ->as($attachment['originalFileName'])
                ->withMime($mimeContentType);
        }

        return $files;
    }
}
