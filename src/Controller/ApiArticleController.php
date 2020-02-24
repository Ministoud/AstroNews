<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ApiArticleController extends AbstractController
{
    protected $repo;
    protected $normalizer;

    public function __construct(ArticleRepository $repo, NormalizerInterface $normalizer)
    {
        $this->repo = $repo;
        $this->normalizer = $normalizer;
    }

    /**
     * @Route("/api/article", name="api_article", methods={"GET"})
     */
    public function index()
    {
        $posts = $this->repo->findAll();
        $normalizedObject = $this->normalizer->normalize($posts, null, ['groups' => 'get:accepted']);

        return $this->render('article/list.html.twig', [
        ]);
    }
}
