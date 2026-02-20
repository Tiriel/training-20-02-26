<?php

namespace App\Notifier\Conference;

use App\Entity\Conference;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackSectionBlock;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Notification\ChatNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\RecipientInterface;

class ConferenceCreatedNotification extends Notification implements ChatNotificationInterface
{
    public function __construct(private readonly Conference $conference)
    {
        parent::__construct('New Conference: ' . $conference->getName(), ['chat']);
    }

    public function asChatMessage(RecipientInterface $recipient, ?string $transport = null): ?ChatMessage
    {
        $options = (new SlackOptions())
            ->block((new SlackSectionBlock())->text(
                '*' . $this->conference->getName() . "*\n" . $this->conference->getDescription()
            ))
            ->block((new SlackSectionBlock())->text(
                sprintf('Starts: %s | <https://127.0.0.1:8000/conference/%d|View Details>',
                    $this->conference->getStartAt()->format('d M Y'),
                    $this->conference->getId()
                )
            ));

        return (new ChatMessage($this->getSubject()))->options($options);
    }
}
