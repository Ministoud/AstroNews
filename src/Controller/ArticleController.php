<?php

namespace App\Controller;

use App\Entity\Article;
use App\Event\ArticleCreated;
use App\Event\ArticleEdited;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Intervention\Image\ImageManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    protected $repo;
    protected $manager;
    protected $dispatcher;

    public function __construct(ArticleRepository $repo, EntityManagerInterface $manager, EventDispatcherInterface $eventDispatcher)
    {
        $this->repo = $repo;
        $this->manager = $manager;
        $this->dispatcher = $eventDispatcher;
    }

    /**
     * @Route("/article", name="listArticles")
     */
    public function listNews()
    {
        $user = $this->getUser();

        // Check if the user is logged in
        if(!empty($user))
        {
            $followedSections = $user->getUseFollowedSections();
        }

        // Check if the user follow a section
        if(!empty($followedSections) && count($followedSections) > 0)
        {
            $secIDs = array();
            foreach ($followedSections as $section) {
                $secIDs[] = $section->getId();
            }

            $articles = $this->repo->findFollowedArticles($secIDs);
        }
        else
        {
            $articles = $this->repo->findAllArticles();
        }

        return $this->render('article/list.html.twig', [
            "articles" => $articles
        ]);
    }

    /**
     * @Route("/article/add", name="addArticle")
     */
    public function addArticle(Request $request, NotifierInterface $notifier)
    {
        $article = new Article();

        $this->denyAccessUnlessGranted('ART_VIEW', $article);

        $user = $this->getUser();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $article->setArtCreationDate(new \DateTime('now'));
            $article->setArtAuthor($user);

            $imageFile = $form->get('artImage')->getData();

            if($imageFile)
            {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                $imageManager = new ImageManager(array('driver' => 'imagick'));
                $newImage = $imageManager->make($imageFile)->resize(240,320);
                $newImage->save($this->getParameter('images_directory') . $newFilename);

                $article->setArtImage($newFilename);
            }

            $this->manager->persist($article);
            $this->manager->flush();

            $this->dispatcher->dispatch(new ArticleCreated($article),ArticleCreated::NAME);

            return $this->redirectToRoute('detailsArticle', array('article' => $article->getId()));
        }

        return $this->render('article/editing.html.twig', [
            'addForm' => $form->createView(),
            'isEditing' => false
        ]);
    }

    /**
     * @Route("/article/edit/{article}", name="editArticle")
     */
    public function editArticle(Article $article, Request $request)
    {
        $this->denyAccessUnlessGranted('ART_EDIT', $article);

        $defaultImageFilename = $article->getArtImage();
        if(!empty($defaultImageFilename))
        {
            $article->setArtImage(
                new File($this->getParameter('images_directory') . $defaultImageFilename)
            );
        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $this->dispatcher->dispatch(new ArticleEdited($article), ArticleEdited::NAME);

            $imageFile = $form->get('artImage')->getData();
            if($imageFile)
            {
                
                if($imageFile != $defaultImageFilename)
                {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                    $imageManager = new ImageManager(array('driver' => 'imagick'));
                    $newImage = $imageManager->make($imageFile)->resize(240,320);
                    $newImage->save($this->getParameter('images_directory') . $newFilename);

                    $article->setArtImage($newFilename);
                }
            }
            else
            {
                $article->setArtImage($defaultImageFilename);
            }

            $article->setArtEditionDate(new \DateTime('now'));
            $this->manager->flush();

            $this->dispatcher->dispatch(new ArticleEdited($article), ArticleEdited::NAME);

            return $this->redirectToRoute('detailsArticle', array('article' => $article->getId()));
        }

        return $this->render('article/editing.html.twig', [
            'addForm' => $form->createView(),
            'isEditing' => 'true'
        ]);
    }

    /**
     * @Route("/article/{article}", name="detailsArticle")
     */
    public function detailsArticle(Article $article)
    {
        return $this->render('article/details.html.twig', [
            "article" => $article
        ]);
        
    }

    /**
     * @Route("/article/remove/{article}", name="removeArticle")
     */
    public function removeArticle(Article $article)
    {
        $this->denyAccessUnlessGranted('ART_EDIT', $article);

        $this->manager->remove($article);
        $this->manager->flush();

        return $this->render('article/remove.html.twig', [
        ]);
    }
}
