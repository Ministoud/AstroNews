<?php

//TODO: Ajouter un champ "description"

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Section;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SectionController extends AbstractController
{
    /**
     * @Route("/section", name="listSections")
     */
    public function list()
    {
        $repo = $this->getDoctrine()->getRepository(Section::class);
        $sections = $repo->findAllSections();

        return $this->render('section/list.html.twig', [
            'sections' => $sections
        ]);
    }

    /**
     * @Route("/section/{section}", name="detailsSection")
     */
    public function getSectionArticles(Section $section)
    {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $articles = $repo->findArticlesBySectionID($section->getId());

        return $this->render('section/details.html.twig', [
            'section' => $section,
            'articles' => $articles
        ]);
    }
}
