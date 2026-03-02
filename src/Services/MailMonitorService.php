<?php

namespace Sahlowle\Larawatch\Services;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use Sahlowle\Larawatch\Models\MonitorMail;
use Symfony\Component\Mime\Email;

class MailMonitorService
{
    /**
     * Record a sent email.
     */
    public function recordSent(MessageSent $event): void
    {
        if (! config('larawatch.features.mail', true)) {
            return;
        }

        $message = $event->message;
        $user = Auth::user();

        MonitorMail::create([
            'mailer' => $this->getMailer($event),
            'to' => $this->formatAddresses($message->getTo()),
            'from' => $this->formatAddresses($message->getFrom()),
            'subject' => $message->getSubject(),
            'status' => 'sent',
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'size' => strlen($message->toString()),
            'cc' => $this->getAddressArray($message->getCc()),
            'bcc' => $this->getAddressArray($message->getBcc()),
        ]);
    }

    /**
     * Record a failed email.
     */
    public function recordFailed(Email $message, string $exception = ''): void
    {
        if (! config('larawatch.features.mail', true)) {
            return;
        }

        $user = Auth::user();

        MonitorMail::create([
            'mailer' => config('mail.default'),
            'to' => $this->formatAddresses($message->getTo()),
            'from' => $this->formatAddresses($message->getFrom()),
            'subject' => $message->getSubject(),
            'status' => 'failed',
            'exception' => $exception,
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'size' => null,
            'cc' => $this->getAddressArray($message->getCc()),
            'bcc' => $this->getAddressArray($message->getBcc()),
        ]);
    }

    protected function formatAddresses(?array $addresses): string
    {
        if (empty($addresses)) {
            return '';
        }

        return collect($addresses)
            ->map(fn ($addr) => $addr->getAddress())
            ->implode(', ');
    }

    protected function getAddressArray(?array $addresses): ?array
    {
        if (empty($addresses)) {
            return null;
        }

        return collect($addresses)
            ->map(fn ($addr) => $addr->getAddress())
            ->values()
            ->toArray();
    }

    protected function getMailer($event): string
    {
        return $event->data['mailer'] ?? config('mail.default', 'smtp');
    }
}
