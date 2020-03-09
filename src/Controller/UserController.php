<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Section;
use App\Form\LoginType;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserController extends AbstractController
{

    protected $manager;
    protected $encoder;

    public function __construct(EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $this->manager = $manager;
        $this->encoder = $encoder;
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authUtils) : Response
    {

        $error = $authUtils->getLastAuthenticationError();

        $lastUsername = $authUtils->getLastUsername();

        return $this->render('user/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request)
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $hash = $this->encoder->encodePassword($user, $form->get('plainPassword')->getData());

            $user->setUsePassword($hash);

            $this->manager->persist($user);
            $this->manager->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render('user/register.html.twig', [
            'registerForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/{user}", name="userDetails")
     */
    public function userDetails(User $user)
    {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $articles = $repo->findArticlesByUserID($user->getId());

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/follow/{section}", name="followSection")
     */
    public function followSection(Section $section, Request $request)
    {
        $user = $this->getUser();

        $this->denyAccessUnlessGranted('USE_VIEW', $user);

        // Check is the section is already followed by the user
        $alreadyFollowed = false;
        $sections = $user->getUseFollowedSections();
        foreach ($sections as $followedSection) {
            if($section == $followedSection)
            {
                $alreadyFollowed = true;
            }
        }

        if($alreadyFollowed)
        {
            $user->removeUseFollowedSection($section);
        }
        else
        {
            $user->addUseFollowedSection($section);
        }

        $this->manager->persist($user);
        $this->manager->flush();

        $request->getSession()->set('referer', $request->headers->get('referer'));
        if($request->getSession()->get('referer'))
        {
            return $this->redirect($request->getSession()->get('referer'));
        }
        else{
            return $this->redirectToRoute('detailsSection', ['section' => $section->getId()]);
        }
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {}
}
