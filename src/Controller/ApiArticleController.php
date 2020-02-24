<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\SectionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Prophecy\Doubler\ClassPatch\ThrowablePatch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiArticleController extends AbstractController
{
    protected $artRepo;
    protected $secRepo;
    protected $useRepo;
    protected $manager;
    protected $serializer;
    protected $validator;

    public function __construct(ArticleRepository $artRepo, UserRepository $useRepo, SectionRepository $secRepo, SerializerInterface $serializer, EntityManagerInterface $manager, ValidatorInterface $validator)
    {
        $this->artRepo = $artRepo;
        $this->useRepo = $useRepo;
        $this->secRepo = $secRepo;
        $this->serializer = $serializer;
        $this->manager = $manager;
        $this->validator = $validator;
    }

    /**
     * @Route("/api/article", name="api_article_index", methods={"GET"})
     */
    public function index()
    {
        return $this->json($this->artRepo->findAllArticles(), 200, [], ["groups" => "get:accepted"]);
    }

    /**
     * @Route("api/article/add", name="api_article_add", methods={"POST"})
     */
    public function addArticle(Request $request)
    {
        $article = new Article();

        try
        {
            $postedJson = $request->getContent();
            $deserializeArticle = $this->serializer->deserialize($postedJson, Article::class, 'json');

            if($deserializeArticle->getArtName())
            {
                $article->setArtName($deserializeArticle->getArtName());
            }

            if($deserializeArticle->getArtContent())
            {
                $article->setArtContent($deserializeArticle->getArtContent());
            }

            if($deserializeArticle->getArtCreationDate())
            {
                $article->setArtCreationDate($deserializeArticle->getArtCreationDate());
            }

            $sections = $deserializeArticle->getArtSections();
            foreach ($sections as $section)
            {
                $section = $this->secRepo->findOneById($section->getId());
                if(!$section)
                {
                    return $this->json([
                        'status' => 400,
                        'message' => 'No Article sections found with one of these IDs'
                    ], 400);
                }
                $article->addArtSection($section);
            }

            if($deserializeArticle->getArtAuthor())
            {
                $author = $this->useRepo->findOneById($deserializeArticle->getArtAuthor()->getId());

                if(!$author)
                {
                    return $this->json([
                        'status' => 400,
                        'message' => 'No Article author found with this ID'
                    ], 400);
                }
                $article->setArtAuthor($author);
            }

            if($deserializeArticle->getArtImage())
            {
                $article->setArtImage($deserializeArticle->getArtImage());
            }

            $errors = $this->validator->validate($article);

            if(count($errors) > 0)
            {
                return $this->json($errors, 400);
            }

            $this->manager->persist($article);
            $this->manager->flush();
        }
        catch (NotEncodableValueException $e)
        {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
        return $this->json($article, 201, [], ['groups' => 'get:accepted']);
    }
}
