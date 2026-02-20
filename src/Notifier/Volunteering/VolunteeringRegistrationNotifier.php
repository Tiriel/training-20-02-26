<?php

namespace App\Notifier\Volunteering;

use App\Entity\Volunteering;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

class VolunteeringRegistrationNotifier
{
    public function __construct(
        private readonly NotifierInterface $notifier,
        private readonly VolunteeringRegistrationDesktopNotifier $desktopNotifier
    ) {}

    public function sendConfirmation(Volunteering $volunteering): void
    {
        $this->notifier->send(
            new VolunteerRegistrationNotification($volunteering),
            new Recipient($volunteering->getForUser()->getEmail())
        );

        $this->desktopNotifier->notifyNewRegistration($volunteering);
    }
}
