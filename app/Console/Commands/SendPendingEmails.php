<?php

namespace App\Console\Commands;

use App\Models\Mail;
use App\Services\MailDeliveryService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendPendingEmails extends Command
{
    protected $signature = 'emails:send-pending {--limit=10 : Maximum number of emails to process per run}';

    protected $description = 'Dispatch pending emails without relying on queue workers.';

    public function handle(MailDeliveryService $mailDeliveryService): int
    {
        $limit = (int) $this->option('limit');
        $limit = $limit > 0 ? $limit : 10;

        $processed = 0;

        Mail::query()
            ->where('is_sent', false)
            ->orderBy('id')
            ->chunkById($limit, function (Collection $mails) use (&$processed, $mailDeliveryService) {
                foreach ($mails as $mail) {
                    try {
                        $mailDeliveryService->send($mail);
                        $processed++;
                    } catch (Throwable $exception) {
                        Log::error(
                            sprintf('Cron-based email send failed for email #%d: %s', $mail->getId(), $exception->getMessage()),
                            ['exception' => $exception]
                        );
                    }
                }

                return false;
            });

        $this->info(sprintf('Processed %d email(s).', $processed));

        return self::SUCCESS;
    }
}
