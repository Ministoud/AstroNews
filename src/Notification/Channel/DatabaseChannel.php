<?php

namespace App\Notification\Channel;

use App\Entity\UserNotification;
use App\Repository\UserNotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Notifier\Channel\AbstractChannel;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\Recipient;

class DatabaseChannel extends AbstractChannel
{
    protected $notifRepo;
    protected $manager;

    public function __construct(UserNotificationRepository $notifRepo, EntityManagerInterface $manager)
    {
        $this->notifRepo = $notifRepo;
        $this->manager = $manager;
    }

    public function notify(Notification $notification, Recipient $recipient, string $transportName = null): void
    {
            $userNotification = (new UserNotification())
                ->setSubject($notification->getSubject())
                ->setContent($notification->getContent())
                ->setImportance($notification->getImportance())
                ->setHasBeenRead(false)
                ->setUser($recipient->getUser())
                ->setAction($notification->getAction())
                ->setSubjectId($notification->getSubjectId());

            $this->manager->persist($userNotification);
            $this->manager->flush();
    }

    public function supports(Notification $notification, Recipient $recipient): bool
    {
        return true;
    }
}