<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
  public const RECIPES_PER_PAGE = 2;
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Recipe::class);
  }
  //retourner total de duration dans la bdd
  /* public function findTotalDuration(): int
    {
        return $this->createQueryBuilder('r')
            ->select('SUM(r.duration)')
            ->getQuery()
            ->getSingleScalarResult();
    } */

  /**
   * @return Recipe[] Returns an array of Recipe objects
   */
  public function findWithDurationLowerThan(int $duration): array
  {
    return $this->createQueryBuilder('r')
      ->select('r', 'c')
      ->where('r.duration <= :duration')
      ->orderBy('r.duration', 'ASC')
      ->leftJoin('r.category', 'c')
      ->setMaxResults(10)
      ->setParameter('duration', $duration)
      ->getQuery()
      ->getResult();
  }
  //donner un resultat pagnated
  public function getRecipePaginator(int $offset): Paginator
  {
    $query = $this->createQueryBuilder('r')
      ->select('r', 'c')
      ->orderBy('r.title', 'ASC')
      ->leftJoin('r.category', 'c')
      ->setMaxResults(self::RECIPES_PER_PAGE) //sql limit
      ->setFirstResult($offset) //sql offset
      ->getQuery();

    return new Paginator($query);
  }

  public function findByCategory($categoryId): array
  {
    $qb = $this->createQueryBuilder('r')
      ->select('r', 'c')
      ->where('r.category = :categoryId')
      ->leftJoin('r.category', 'c')
      ->setParameter('categoryId', $categoryId);
    $result = $qb->getQuery()->getResult();
    return $result;
  }

  public function findByUserId($user): ?array
  {
    return $this->createQueryBuilder('r')
      ->andWhere('r.user = :user')
      ->setParameter('user', $user)
      ->getQuery()
      ->getArrayResult()
    ;
  }
  public function getAllRecipes(): array
  {
    return $this->createQueryBuilder('r')
      ->select('r', 'c')
      ->leftJoin('r.category', 'c')
      ->getQuery()
      ->getArrayResult();
  }

  //    public function findOneBySomeField($value): ?Recipe
  //    {
  //        return $this->createQueryBuilder('r')
  //            ->andWhere('r.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->getQuery()
  //            ->getOneOrNullResult()
  //        ;
  //    }
}
