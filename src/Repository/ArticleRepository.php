<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @param $sectionIDs
     * @return Article[] Returns an array of Articles followed by the user
     */
    public function findFollowedArticles($sectionIDs)
    {
        $qb = $this->createQueryBuilder('art');
        return $qb->select('art', 'sec', 'aut')
            ->add('where', $qb->expr()->in('sec.id', ':secIDs'))
            ->setParameter('secIDs', $sectionIDs)
            ->leftJoin('art.artSections', 'sec')
            ->leftJoin('art.artAuthor', 'aut')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
    * @return Article[] Returns an array of all Articles 
    */
    public function findAllArticles()
    {
        return $this->createQueryBuilder('art')
            ->select('art', 'sec', 'aut')
            ->leftJoin('art.artSections', 'sec')
            ->leftJoin('art.artAuthor', 'aut')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $secID
     * @return Article[] Returns an array of section's Articles
     */
    public function findArticlesBySectionID($secID)
    {
        return $this->createQueryBuilder('art')
            ->select('art', 'sec', 'aut')
            ->setParameter('secID', $secID)
            ->where('sec.id LIKE :secID')
            ->leftJoin('art.artSections', 'sec')
            ->join('art.artAuthor', 'aut')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $userID
     * @return Article[] Returns an array of all Sections
     */
    public function findArticlesByUserID($userID)
    {
        return $this->createQueryBuilder('art')
            ->select('sec', 'aut', 'art')
            ->setParameter('userID', $userID)
            ->where('aut = :userID')
            ->leftJoin('art.artSections', 'sec')
            ->join('art.artAuthor', 'aut')
            ->getQuery()
            ->getResult();
    }
}
