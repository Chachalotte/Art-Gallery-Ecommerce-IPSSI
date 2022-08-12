<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /*
    * findSuccessOrders()
    * récupérer les commandes payées
    */
    public function findSuccessOrders($user)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.state > 0')
            ->andWhere('o.user = :user')
            ->setParameter('user', $user)
            ->orderBy('o.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

     /*
    * findPaidOrders()
    * récupérer les commandes payées
    */
    public function findPaidOrders()
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.state > 0')
            ->orderBy('o.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /*
        La requête retourne tous les orders où createdAt est entre dateStart et dateNow
        La valeur de dateStart est de 30j avant dateNow
        Les dates prennent format année-mois-jour puis heure:minute:second
    */
    public function findByThirtyDays($dateStart, $dateNow) {
        return $this->createQueryBuilder('o')
            ->andWhere('o.state > 0')
            ->andWhere('o.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $dateStart->format('Y-m-d H:m:s'))
            ->setParameter('end', $dateNow->format('Y-m-d H:m:s'))
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Order[] Returns an array of Order objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Order
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
