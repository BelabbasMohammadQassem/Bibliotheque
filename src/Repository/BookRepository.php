<?php
namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function findBooksWithAuthorsAndCategories()
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.authors', 'a')
            ->leftJoin('b.categories', 'c')
            ->addSelect('a', 'c')
            ->getQuery()
            ->getResult();
    }

    public function searchBooks(?string $query, ?array $categories = null)
    {
        $qb = $this->createQueryBuilder('b');

        if ($query) {
            $qb->andWhere('b.title LIKE :query')
               ->setParameter('query', "%{$query}%");
        }

        if ($categories) {
            $qb->innerJoin('b.categories', 'cat')
               ->andWhere('cat.id IN (:categories)')
               ->setParameter('categories', $categories);
        }

        return $qb->getQuery()->getResult();
    }

    public function getPaginatedBooks(int $page = 1, int $limit = 10)
    {
        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.authors', 'a')
            ->leftJoin('b.categories', 'c')
            ->addSelect('a', 'c')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function countTotalBooks()
    {
        return $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}