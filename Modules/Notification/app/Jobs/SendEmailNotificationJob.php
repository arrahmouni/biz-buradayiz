<?php

namespace Modules\Notification\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Notification\Models\Notification;
use Illuminate\Queue\Attributes\WithoutRelations;
use Modules\Notification\Mail\NotificationMailable;
use Modules\Notification\Events\EmailSentFailedEvent;
use Modules\Notification\Events\EmailSentSuccessfullyEvent;

class SendEmailNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private string $email, private string $name, #[WithoutRelations] private Notification $notification, private string $title, private string $body, private array $extraData = [], private ?string $locale = null)
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger()->info('Sending email notification', [
            'email' => $this->email,
            'name' => $this->name,
            'title' => $this->title,
            'body' => $this->body,
            'locale' => $this->locale ?? app()->getLocale(),
            'extraData' => $this->extraData,
        ]);

        try {
            $mailable = new NotificationMailable($this->name, $this->body, $this->extraData);

            Mail::to($this->email, $this->name)
                ->send($mailable->subject($this->title));

            event(new EmailSentSuccessfullyEvent($this->notification));
        } catch (\Exception $e) {
            logger()->error('Failed to send email notification', [
                'email' => $this->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            event(new EmailSentFailedEvent($this->notification));
        }

    }
}
