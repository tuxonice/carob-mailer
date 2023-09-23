<?php

namespace Tests\Feature\Api;

use App\Mail\MailSent;
use App\Models\Mail;
use Tests\TestCase;

class MailSentTest extends TestCase
{
    public function test_mailable_content(): void
    {
        $mail = new Mail();
        $mail
            ->setFromName('test source user name')
            ->setToName('test target user name')
            ->setToEmail('user@example.com')
            ->setSubject('test subject')
            ->setBodyText('test sample body text')
            ->setBodyHtml('<b>test sample body html</b>');

        $mailable = new MailSent($mail);

        $mailable->assertFrom('noreply@example.com', 'test source user name');
        $mailable->assertHasTo('user@example.com', 'test target user name');
        $mailable->assertHasSubject('test subject');
        $mailable->assertSeeInHtml('test sample body html');
        $mailable->assertSeeInText('test sample body text');
    }
}
