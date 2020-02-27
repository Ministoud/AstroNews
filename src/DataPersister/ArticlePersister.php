<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;

final class ArticlePersister implements ContextAwareDataPersisterInterface
{

    protected $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Article;
    }

    public function persist($data, array $context = [])
    {
        $data->setArtCreationDate(new \DateTime());
dd($data);
        $this->manager->merge($data);
        $this->manager->flush();
        return $data;
    }

    public function remove($data, array $context = [])
    {
        $this->manager->remove($data);
        $this->manager->flush();
    }
}