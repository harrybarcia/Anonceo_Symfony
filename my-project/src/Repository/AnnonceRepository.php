<?php

namespace App\Repository;

use App\Entity\Annonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use src\data\SearchData;

/**
 * @method Annonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonce[]    findAll()
 * @method Annonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

   /**
    *  Récupère les annonces en lien avec une recherche
    * @return Annonce[]
    */
    public function rechercher(SearchData $search): array
    {
        $query=$this
        ->createQueryBuilder('an')
        ->select('c', 'an')
        ->join('an.categorie', 'c');
        
        if(!empty($search->q)){
            $query=$query
                ->andWhere('an.titre LIKE:q')
                ->setParameter('q',"%{$search->q}%");

        }
        if(!empty($search->categorie)){
            $query=$query
                ->andWhere('c.id IN (:categorie)')
                ->setParameter('categorie',$search->categorie);

        }
        // ->join('an.categories', 'c')
        return $query->getQuery()->getResult();
    }
}
