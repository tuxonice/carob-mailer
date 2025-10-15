<?php

namespace App\Services;

use App\Mail\MailSent;
use App\Models\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail as Mailer;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MailDeliveryService
{
    public function send(Mail $mail): void
    {
        $mail->refresh();

        if ($mail->isSent()) {
            Log::info(sprintf('Email #%d already sent, skipping.', $mail->getId()));

            return;
        }

        try {
            Mailer::send(new MailSent($mail));
            $this->deleteAttachmentsFromStorage($mail->getAttachments());

            $mail->setIsSent(true);
            $mail->save();

            Log::info(
                sprintf(
                    'Email #%d sent at %s',
                    $mail->getId(),
                    now()->toDateTimeString()
                )
            );
        } catch (Throwable $e) {
            Log::error(
                sprintf('Email #%d sending failed: %s', $mail->getId(), $e->getMessage()),
                ['exception' => $e]
            );

            throw $e;
        }
    }

    private function deleteAttachmentsFromStorage(string $attachments): void
    {
        $decoded = json_decode($attachments, true) ?? [];

        if (empty($decoded)) {
            return;
        }

        $files = array_map(
            static fn (array $attachment): ?string => $attachment['attachFileName'] ?? null,
            $decoded
        );

        $files = array_filter($files);

        if (! empty($files)) {
            Storage::disk('attachments')->delete($files);
        }
    }
}
