<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Article;
use App\Repository\SectionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

final class ArticlePersister implements ContextAwareDataPersisterInterface
{

    protected $manager;
    protected $useRepo;
    protected $secRepo;

    public function __construct(EntityManagerInterface $manager, UserRepository $useRepo, SectionRepository $secRepo)
    {
        $this->manager = $manager;
        $this->secRepo = $secRepo;
        $this->useRepo = $useRepo;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Article;
    }

    public function persist($data, array $context = [])
    {
//        Check if the article has a date (is already created) and add an edition date if true
        if(!$data->getArtCreationDate())
        {
            $data->setArtCreationDate(new \DateTime());
        }
        else
        {
            $data->setArtEditionDate(new \DateTime());
        }

        $sections = $data->getArtSections();
        $data->clearArtSections();
        foreach ($sections as $section)
        {
            $section = $this->secRepo->findOneById($section->getId());
            if(!$section)
            {
                return $this->json([
                    'status' => 404,
                    'message' => 'No Article sections found with one of these IDs'
                ], 404);
            }
            $data->addArtSection($section);
        }

        if($data->getArtAuthor())
        {
            $author = $this->useRepo->findOneById($data->getArtAuthor()->getId());

            if(!$author)
            {
                return $this->json([
                    'status' => 404,
                    'message' => 'No Article author found with this ID'
                ], 404);
            }
            $data->setArtAuthor($author);
        }

        $this->manager->persist($data);
        $this->manager->flush();
        return $data;
    }

    public function remove($data, array $context = [])
    {
        $this->manager->remove($data);
        $this->manager->flush();
    }
}