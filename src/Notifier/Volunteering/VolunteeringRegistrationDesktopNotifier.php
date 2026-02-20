<?php

namespace App\Notifier\Volunteering;

use App\Entity\Volunteering;
use Symfony\Component\Notifier\Message\DesktopMessage;
use Symfony\Component\Notifier\TexterInterface;

class VolunteeringRegistrationDesktopNotifier
{
    public function __construct(private readonly TexterInterface $texter) {}

    public function notifyNewRegistration(Volunteering $volunteering): void
    {
        $this->texter->send(new DesktopMessage(
            'New Volunteer registration',
            sprintf('%s registered for %s',
                $volunteering->getForUser()->getEmail(),
                $volunteering->getConference()->getName()
            )
        ));
    }
}
