<?php

namespace Modules\Notification\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationMailable extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        private string $name,
        private string $content,
        private array $extraData = []
    ) {
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        $fromEmail = config('notification.sendgrid.from_email', config('mail.from.address'));
        $fromName = config('notification.sendgrid.from_name', config('mail.from.name'));

        return $this->from($fromEmail, $fromName)
            ->view('admin::emails.templates.template', [
                'content'    => $this->content,
                'userName'   => $this->name,
                'greeting'   => $this->extraData['greeting'] ?? null,
                'actionUrl'  => $this->extraData['actionUrl'] ?? null,
                'actionText' => $this->extraData['actionText'] ?? null,
            ]);
    }
}

