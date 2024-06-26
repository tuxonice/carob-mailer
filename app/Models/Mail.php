<?php

namespace App\Models;

use Database\Factories\UserFactory;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mail extends Model
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    protected $table = 'emails';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFromName(): ?string
    {
        return $this->from_name;
    }

    public function setFromName(string $fromName): self
    {
        $this->from_name = $fromName;

        return $this;
    }

    public function getToName(): ?string
    {
        return $this->to_name;
    }

    public function setToName(string $toName): self
    {
        $this->to_name = $toName;

        return $this;
    }

    public function getToEmail(): ?string
    {
        return $this->to_email;
    }

    public function setToEmail(string $toEmail): self
    {
        $this->to_email = $toEmail;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getBodyText(): ?string
    {
        return $this->body_text;
    }

    public function setBodyText(string $bodyText): self
    {
        $this->body_text = $bodyText;

        return $this;
    }

    public function getBodyHtml(): ?string
    {
        return $this->body_html;
    }

    public function setBodyHtml(string $bodyHtml): self
    {
        $this->body_html = $bodyHtml;

        return $this;
    }

    public function getAttachments(): string
    {
        return $this->attachments ?? '';
    }

    public function setAttachments(string $attachments): self
    {
        $this->attachments = $attachments;

        return $this;
    }

    public function isSent(): ?bool
    {
        return $this->is_sent;
    }

    public function setIsSent(bool $isSent): self
    {
        $this->is_sent = $isSent;

        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updated_at;
    }
}
