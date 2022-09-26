<?php

namespace App\Repository;

use App\Entity\Tag;
use App\Entity\Post;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function add(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
    * @return Post[] Returns an array of Post objects
    */
    public function getFilterPosts($category_id): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isPublished = :val')
            ->andWhere('p.category = :category_id')
            ->setParameter('val', true)
            ->setParameter('category_id', $category_id)
            ->orderBy('p.id', 'DESC')
         //    ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    public function filterPostBytag(Tag $tag): array
    {
        return $this->createQueryBuilder('p')
            // ->andWhere('p.isPublished = :val')
            // ->andWhere('p.tag = :tag')
            // ->setParameter('val', true)
            // ->setParameter('tag_id', $tag_id)
            // ->orderBy('p.id', 'DESC')
            // ->getQuery()
            // ->getResult()
            ->join('p.tags', 't')
            ->select('p')
            ->join('p.tags', 'tmp')
            ->where('tmp.id = :id')
            ->addSelect('t')
            ->andWhere('p.isPublished = :val')
            ->setParameter('id', $tag->getId())
            ->setParameter('val', true)
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?Post
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
