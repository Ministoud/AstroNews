<?php

namespace App\Controller;

use App\Entity\UserNotification;
use App\Entity\UserNotificationSettings;
use App\Form\SettingsType;
use App\Notification\CustomNotification;
use App\Repository\UserNotificationSettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    public $manager;
    public $repo;

    public function __construct(EntityManagerInterface $manager, UserNotificationSettingsRepository $repo)
    {
        $this->manager = $manager;
        $this->repo = $repo;
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

        return null;
    }

    /**
     * @Route("/notification/settings", name="notifSettings")
     */
    public function settings(Request $request)
    {
        //TODO: Récupérer la liste des events et dans une boucle foreach créer automatiquement les SettingType
        $user = $this->getUser();
        $this->denyAccessUnlessGranted('USE_VIEW', $user);

        $createSettings = $this->repo->findOneBy([
            "name" => 'article_created',
            "user" => $this->getUser()
        ]);

        $editSettings = $this->repo->findOneBy([
            "name" => 'article_edited',
            "user" => $this->getUser()
        ]);

        if(empty($createSettings))
        {
            $createSettings = new UserNotificationSettings();
            $createSettings
                ->setName('article_created')
                ->setUser($this->getUser());
        }

        if(empty($editSettings))
        {
            $editSettings = new UserNotificationSettings();
            $editSettings
                ->setName('article_edited')
                ->setUser($this->getUser());
        }

        $form = $this->createForm(SettingsType::class, [
            'settings' => [$createSettings, $editSettings],
            'labels' => [
                'Création d\'un article dans l\'une des catégories que vous suivez',
                'Modification d\'un de vos article par un administrateur'
            ]
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->manager->persist($createSettings);
            $this->manager->persist($editSettings);
            $this->manager->flush();
        }

        return $this->render('notification/settings.html.twig', [
            'settingsForm' => $form->createView()
        ]);
    }
}
