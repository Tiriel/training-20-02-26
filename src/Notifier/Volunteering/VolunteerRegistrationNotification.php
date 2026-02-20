<?php

namespace App\Notifier\Volunteering;

use App\Entity\Volunteering;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;

class VolunteerRegistrationNotification extends Notification implements EmailNotificationInterface
{
    public function __construct(private readonly Volunteering $volunteering)
    {
        parent::__construct('New volunteering registered');
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): ?EmailMessage
    {
        $email = (new NotificationEmail())
            ->from('admin@sensio-events.com')
            ->to($this->volunteering->getForUser()->getEmail())
            ->subject($this->getSubject())
            ->htmlTemplate('emails/volunteer_registration.html.twig')
            ->context(['volunteering' => $this->volunteering])
            ;

        return new EmailMessage($email);
    }
}
