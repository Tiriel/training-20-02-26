<?php

namespace App\Workflow;

use App\Entity\Volunteering;
use Symfony\Component\Workflow\Attribute\AsGuardListener;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\TransitionBlocker;
use function Symfony\Component\Clock\now;

class VolunteeringGuardListener
{
    #[AsGuardListener(workflow: 'volunteering_status', transition: 'approve')]
    public function guardApprove(GuardEvent $event): void
    {
        /** @var Volunteering $volunteering */
        $volunteering = $event->getSubject();
        if ($volunteering->getStartAt() <= now()) {
            $event->addTransitionBlocker(new TransitionBlocker(
                'Cannot approve a volunteering shift that has already started.',
                'volunteering.shift_started'
            ));
        }
    }

    #[AsGuardListener(workflow: 'volunteering_status', transition: 'activate')]
    public function guardActivate(GuardEvent $event): void
    {
        /** @var Volunteering $volunteering */
        $volunteering = $event->getSubject();
        if ($volunteering->getStartAt() > now()) {
            $event->addTransitionBlocker(new TransitionBlocker(
                'Cannot activate a volunteering shift that has not started yet.',
                'volunteering.shift_not_started'
            ));
        }
    }
}
