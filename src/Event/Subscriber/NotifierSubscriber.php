<?php

namespace App\Event\Subscriber;

use App\Event\ArticleCreated;
use App\Event\ArticleEdited;
use App\Notification\CustomNotification;
use App\Notification\Recipient\UserRecipient;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class NotifierSubscriber implements EventSubscriberInterface
{
    protected $notifier;
    protected $token;

    public function __construct(NotifierInterface $notifier, TokenStorageInterface $token)
    {
        $this->notifier = $notifier;
        $this->token = $token;
    }

    public static function getSubscribedEvents()
    {
        return [
            ArticleCreated::NAME => 'onArticleCreation',
            ArticleEdited::NAME => 'onArticleEdition'
        ];
    }

    public function onArticleCreation(ArticleCreated $event)
    {
        $notification = (new CustomNotification())
            ->subject('Un nouvel article a été créé !')
            ->content('L\'article "' . $event->getArticle()->getArtName() . '" a été créé dans au moins une section que vous suivez !')
            ->importance(CustomNotification::IMPORTANCE_LOW)
            ->channels(['database'])
            ->action(CustomNotification::ACTION_ARTICLE_REDIRECT)
            ->subjectId($event->getArticle()->getId());

        $users[] = null;
        foreach($event->getArticle()->getArtSections() as $section)
        {
            $secUsers = $section->getSecUsers();
            foreach ($secUsers as $user)
            {
                if(!in_array($user,$users) && $user != $event->getArticle()->getArtAuthor())
                {
                    $users[] = $user;
                }
            }
        }

        $recipients = null;
        foreach ($users as $user)
        {
            if($user)
            {
                $recipients[] = new UserRecipient($user);
            }
        }
        $this->notifier->send($notification, ...$recipients);
    }

    public function onArticleEdition(ArticleEdited $event)
    {
        $user = $event->getArticle()->getArtAuthor();
        if($user != $this->token->getToken()->getUser())
        {
            $notification = (new CustomNotification())
                ->subject('Modification d\'un de vos articles')
                ->content('Un de vos articles a été modifié par un administrateur !')
                ->importance(CustomNotification::IMPORTANCE_MEDIUM)
                ->action(CustomNotification::ACTION_ARTICLE_REDIRECT)
                ->subjectId($event->getArticle()->getId())
                ->channels(['database']);

            $recipient = new UserRecipient($user);

            $this->notifier->send($notification, $recipient);
        }
    }
}