<?php

namespace Tests\Feature;

use App\Mail\MailSent;
use App\Models\Mail;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Mail as MailFake;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class SendMailTest extends TestCase
{
    public function test_mail_can_be_sent(): void
    {
        MailFake::fake();

        $mail = new Mail();
        $mail
            ->setFromName('test source user name')
            ->setToName('test target user name')
            ->setToEmail('user@example.com')
            ->setSubject('test subject')
            ->setBodyText('test sample body text')
            ->setBodyHtml('<b>test sample body html</b>');

        $mailable = new MailSent($mail);
        MailFake::send($mailable);

        MailFake::assertSent(MailSent::class);
    }

    public function test_send_mail_with_attachments(): void
    {
        Storage::fake('attachments');
        MailFake::fake();

        $fileContent = base64_decode('VGhpcyBpcyBhIGJhc2UgNjQgc3RyaW5n');
        $attachFileName = Str::uuid()->toString();
        Storage::disk('attachments')->put($attachFileName, $fileContent);
        $attachements[] = [
            'attachFileName' => $attachFileName,
            'originalFileName' => 'sample.txt',
        ];

        $mail = new Mail();
        $mail
            ->setFromName('test source user name')
            ->setToName('test target user name')
            ->setToEmail('user@example.com')
            ->setSubject('test subject')
            ->setBodyText('test sample body text')
            ->setBodyHtml('<b>test sample body html</b>')
            ->setAttachments(json_encode($attachements));

        $mailable = new MailSent($mail);
        MailFake::send($mailable);

        MailFake::assertSent(MailSent::class);

        MailFake::assertSent(MailSent::class, function (MailSent $mail) use ($attachFileName) {
            return $mail->hasAttachment(
                Attachment::fromStorageDisk('attachments', $attachFileName)
                    ->as('sample.txt')
                    ->withMime('text/plain')
            );
        });
    }
}
