<?php

namespace App\Repository;

use App\Entity\Child;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Child|null find($id, $lockMode = null, $lockVersion = null)
 * @method Child|null findOneBy(array $criteria, array $orderBy = null)
 * @method Child[]    findAll()
 * @method Child[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChildRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Child::class);
    }

    // /**
    //  * @return Child[] Returns an array of Child objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Child
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /* Création de ma méthode afin de rechercher un enfant avec en paramètre ma variable '$word' */
    public function getByWordInChild($word)
    {
        /* Le 'queryBuilder' permet de créer des requêtes SELECT en base de données */
        /* En paramètre de ma méthode 'createQueryBuilder' je définis un mot qui fera office d'alias
        pour ma table */
        $queryBuilder = $this->createQueryBuilder('child');
        /* J'effectue ici mon SELECT, dans ma table CHILD */
        $query = $queryBuilder->select('child')
            /* Et, je définis mes critères par le biais de la clause WHERE :
            Recherche du mot dans le nom ou le prénom de l'enfant */
            ->where('child.firstName LIKE :word OR child.lastName LIKE :word')
            /* Afin que ma variable de recherche soit sécurisée, j'utilise 'setParameter' */
            ->setParameter('word', '%' . $word . '%')
            /* Je récupère la requête générée par le query builder */
            ->getQuery();
        /* J'exécute la requête et je récupère les résultats */
        $results = $query->getResult();
        /* Enfin, je retourne les résultats */
        return $results;
    }
}
