<?php

namespace App\Workflow;

use App\Entity\Volunteering;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Workflow\Attribute\AsEnteredListener;
use Symfony\Component\Workflow\Event\EnteredEvent;

class VolunteeringApprovalListener
{
    public function __construct(private readonly MailerInterface $mailer) {}

    #[AsEnteredListener(workflow: 'volunteering_assignment', place: 'approved')]
    public function onApproved(EnteredEvent $event): void
    {
        /** @var Volunteering $volunteering */
        $volunteering = $event->getSubject();
        $this->mailer->send((new Email())
            ->to($volunteering->getForUser()->getEmail())
            ->from('admin@sensio-events.com')
            ->subject('Your volunteer application has been approved!')
            ->text(sprintf('You are confirmed for %s.', $volunteering->getConference()->getName()))
        );
    }

}
