<?php

namespace App\Event\Subscriber;

use App\Event\ArticleCreated;
use App\Event\ArticleEdited;
use App\Notification\CustomNotification;
use App\Notification\Recipient\UserRecipient;
use App\Repository\UserNotificationSettingsRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class NotifierSubscriber implements EventSubscriberInterface
{
    protected $notifier;
    protected $token;
    protected $settingsRepo;

    public function __construct(NotifierInterface $notifier, TokenStorageInterface $token, UserNotificationSettingsRepository $settingsRepo)
    {
        $this->notifier = $notifier;
        $this->token = $token;
        $this->settingsRepo = $settingsRepo;
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
                    $userSetting = $this->settingsRepo->findOneBy([
                        'name' => 'article_created',
                        'user' => $user
                    ]);

                    $channels= [];
                    if($userSetting->getChanBrowser())
                    {
                        $channels[] = 'browser';
                    }

                    if($userSetting->getChanChat())
                    {
                        //$channels[] = 'chat';
                    }

                    if($userSetting->getChanEmail())
                    {
                        //$channels[] = 'email';
                    }

                    if($userSetting->getChanDatabase())
                    {
                        $channels[] = 'database';
                    }

                    if($userSetting->getChanSms())
                    {
                        //$channels[] = 'sms';
                    }

                    if(empty($channels))
                    {
                        $notification->channels(['database']);
                    }
                    else
                    {
                        $notification->channels($channels);
                    }

                    $users[] = $user;
                    $recipient = new UserRecipient($user);

                    $this->notifier->send($notification, $recipient);
                }
            }
        }
    }

    public function onArticleEdition(ArticleEdited $event)
    {
        $notification = (new CustomNotification())
            ->subject('Modification d\'un de vos articles')
            ->content('Un de vos articles a été modifié par un administrateur !')
            ->importance(CustomNotification::IMPORTANCE_MEDIUM)
            ->action(CustomNotification::ACTION_ARTICLE_REDIRECT)
            ->subjectId($event->getArticle()->getId());

        $user = $event->getArticle()->getArtAuthor();
        if($user != $this->token->getToken()->getUser())
        {
            $userSetting = $this->settingsRepo->findOneBy([
                'name' => 'article_edited',
                'user' => $user
            ]);

            $channels= [];
            if($userSetting->getChanBrowser())
            {
                $channels[] = 'browser';
            }

            if($userSetting->getChanChat())
            {
                //$channels[] = 'chat';
            }

            if($userSetting->getChanEmail())
            {
                //$channels[] = 'email';
            }

            if($userSetting->getChanDatabase())
            {
                $channels[] = 'database';
            }

            if($userSetting->getChanSms())
            {
                //$channels[] = 'sms';
            }

            if(empty($channels))
            {
                $notification->channels(['database']);
            }
            else
            {
                $notification->channels($channels);
            }

            $recipient = new UserRecipient($user);
            $notification->channels($channels);

            $this->notifier->send($notification, $recipient);
        }
    }
}