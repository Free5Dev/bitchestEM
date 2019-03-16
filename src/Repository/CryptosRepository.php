<?php

namespace App\Repository;

use App\Entity\Cryptos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Cryptos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cryptos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cryptos[]    findAll()
 * @method Cryptos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CryptosRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Cryptos::class);
    }

    // /**
    //  * @return Cryptos[] Returns an array of Cryptos objects
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
    public function findOneBySomeField($value): ?Cryptos
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
