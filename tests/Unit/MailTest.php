<?php

namespace Tests\Unit;

use App\Models\Mail;
use PHPUnit\Framework\TestCase;

class MailTest extends TestCase
{
    public function test_mail_model(): void
    {
        $mail = new Mail();
        $mail
            ->setToName('test name')
            ->setToEmail('user@example.com')
            ->setFromName('test user')
            ->setSubject('test subject')
            ->setBodyText('text version content')
            ->setBodyHtml('html version content')
            ->setAttachments('{}');

        $this->assertEquals('test name', $mail->getToName());
        $this->assertEquals('user@example.com', $mail->getToEmail());
        $this->assertEquals('test user', $mail->getFromName());
        $this->assertEquals('test subject', $mail->getSubject());
        $this->assertEquals('text version content', $mail->getBodyText());
        $this->assertEquals('html version content', $mail->getBodyHtml());
        $this->assertEquals('{}', $mail->getAttachments());
    }
}
