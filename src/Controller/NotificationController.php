<?php

namespace App\Controller;

use App\Entity\UserNotification;
use App\Notification\CustomNotification;
use App\Setting\SettingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    public $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/notification/action/{notification}", name="notifAction")
     */
    public function action(UserNotification $notification, Request $request)
    {
        $notification->setHasBeenRead(true);
        $this->manager->persist($notification);
        $this->manager->flush();

        switch ($notification->getAction())
        {
            case CustomNotification::ACTION_ARTICLE_REDIRECT:
                return $this->redirectToRoute('detailsArticle', array('article' => $notification->getSubjectId()));
                break;
            case CustomNotification::ACTION_NONE:
                $request->getSession()->set('referer', $request->headers->get('referer'));
                return $this->redirect($request->getSession()->get('referer'));
                break;
            default:
                break;
        }
    }

    /**
     * @Route("/notification/settings", name="notifSettings")
     */
    public function settings()
    {
        $this->denyAccessUnlessGranted('USE_VIEW', $this->getUser());



        return $this->render('notification/settings.html.twig', [

        ]);
    }
}
