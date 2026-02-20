<?php

namespace App\Notifier\Conference;

use App\Entity\Conference;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\NoRecipient;

class ConferenceCreatedNotifier
{
    public function __construct(private readonly NotifierInterface $notifier) {}

    public function notifyNewConference(Conference $conference): void
    {
        $this->notifier->send(
            new ConferenceCreatedNotification($conference),
            new NoRecipient()
        );
    }
}
