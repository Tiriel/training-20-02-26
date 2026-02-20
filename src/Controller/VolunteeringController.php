<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Entity\Volunteering;
use App\Enum\VolunteeringTransitions;
use App\Form\VolunteeringType;
use App\Notifier\Volunteering\VolunteeringRegistrationNotifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Workflow\WorkflowInterface;

final class VolunteeringController extends AbstractController
{
    #[Route('/volunteering/{id}', name: 'app_volunteering_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Volunteering $volunteering): Response
    {
        return $this->render('volunteering/show.html.twig', [
            'volunteering' => $volunteering,
        ]);
    }

    #[Route('/volunteering/new', name: 'app_volunteering_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager, VolunteeringRegistrationNotifier $notifier): Response
    {
        $volunteering = (new Volunteering())->setForUser($this->getUser());
        $options = [];

        if ($request->query->get('conference')) {
            $conference = $manager->getRepository(Conference::class)->find($request->get('conference'));
            $volunteering->setConference($conference);
            $options['conference'] = $conference;
        }

        $form = $this->createForm(VolunteeringType::class, $volunteering, $options);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($volunteering);
            $manager->flush();

            $notifier->sendConfirmation($volunteering);

            return $this->redirectToRoute('app_volunteering_show', ['id' => $volunteering->getId()]);
        }

        return $this->render('volunteering/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/volunteering/{id}/transition/{transition}', name: 'app_volunteering_transition', requirements: ['id' => '\d+', 'transition' => 'approve|activate|complete|cancel'])]
    public function transition(
        Volunteering $volunteering,
        string $transition,
        WorkflowInterface $volunteeringStatusStateMachine,
        EntityManagerInterface $manager
    ): Response {
        $transition = VolunteeringTransitions::tryFrom($transition);

        if (null === $transition || !$volunteeringStatusStateMachine->can($volunteering, $transition->value)) {
            $this->addFlash('This transition is not available or not configured');

            return $this->redirectToRoute('app_volunteering_show', ['id' => $volunteering->getId()]);
        }

        $volunteeringStatusStateMachine->apply($volunteering, $transition->value);
        $manager->flush();

        return $this->redirectToRoute('app_volunteering_show', ['id' => $volunteering->getId()]);
    }
}
