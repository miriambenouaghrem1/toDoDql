<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
public function findBySearchTerm($searchTerm)
{
    return $this->createQueryBuilder('b')
        ->andWhere('b.title LIKE :term')
        ->setParameter('term', '%' . $searchTerm . '%')
        ->getQuery()
        ->getResult();
}
public function findBySearchTermbyName($searchTerm)
{
    return $this->createQueryBuilder('b')
        ->join('b.Authors', 'a') // Join the 'authors' relationship
        ->andWhere('a.name = :term') // Use = for exact id match
        ->setParameter('term', $searchTerm)
        ->getQuery()
        ->getResult();
}
public function findBySearchTermbyId($searchTerm)
{
    return $this->createQueryBuilder('b')
        ->join('b.Authors', 'a') // Join the 'authors' relationship
        ->andWhere('a.id = :term') // Use = for exact id match
        ->setParameter('term', $searchTerm)
        ->getQuery()
        ->getResult();
}
public function booksListByAuthor()
{
    $entityManager = $this->getEntityManager();
    
    $query = $entityManager
        ->createQueryBuilder()
        ->select('b') // Select the Book entity
        ->from('App\Entity\Book', 'b')
        ->orderBy('b.Authors', 'ASC') // Assuming 'authors' is the field in the Book entity related to the author
        ->getQuery()
        ->getResult();

    return $query;
}
public function modifyCat($newcategory,$oldcategory){
    $qb = $this->createQueryBuilder('b')
    ->update()
    ->set('b.category', ':newcategory')
    ->where('b.category = :category')
    ->setParameter('newcategory', $newcategory)
    ->setParameter('category', $oldcategory);

return $qb->getQuery()->execute();
}
public function countByCat($categoryName) {
    $qb = $this->createQueryBuilder('b')
        ->select('COUNT(b.id)') // Counting the records by their ID
        ->where('b.category = :categoryName') // Use '=' for equality
        ->setParameter('categoryName', $categoryName);
    
    return $qb->getQuery()->getSingleScalarResult();
}
public function showBooksBetweenDates($dateInf,$dateSup){
    $dql = "SELECT b FROM App\Entity\Book b
    WHERE b.publicationDate BETWEEN :dateInf AND :dateSup";

return $this->getEntityManager()
->createQuery($dql)
->setParameter('dateInf', $dateInf)
->setParameter('dateSup', $dateSup)
->getResult();
}

}