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

//Création d'une méthode : selectionner un article en fonction d'un mot défini et contenu dans l'article
    public function getByWordInArticle($word)
    {

        $queryBuilder = $this->createQueryBuilder('article');
        $query = $queryBuilder->select('article')
            ->where('article.content LIKE :word OR article.title LIKE :word OR article.resume LIKE :
            word')
            //SECURITE
            ->setParameter('word', '%' . $word . '%')
            ->getQuery();

        $results = $query->getResult();
        //Méthode qui me retourne les résultats que l'on va ensuite appeler (afficher) dans mon Articlecontroller
        return $results;
    }

}
