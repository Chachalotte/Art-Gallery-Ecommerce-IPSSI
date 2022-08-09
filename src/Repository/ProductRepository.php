<?php

namespace App\Repository;

use App\Data\SearchProduct;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return Product[] 
     * Returns an array of Product objects filtered
     */
    public function filterProduct(SearchProduct $search)
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('c', 'p')
            ->join('p.category', 'c')
            ->andWhere('p.isSold = 0');


        if(!empty($search->string)){
            $query = $query
                ->andWhere('p.name LIKE :string')
                ->setParameter('string', "%{$search->string}%");
        }

        if(!empty($search->categories)){
            $query = $query
                        ->andWhere('c.id IN (:categories)')
                        ->setParameter('categories', $search->categories);
        }

        if(!empty($search->getMaxPrice())){
            $query = $query
                        ->andWhere('p.price <= :maxprice')
                        ->setParameter('maxprice', $search->getMaxPrice());
        }

        if(!empty($search->getMinPrice())){
            $query = $query
                        ->andWhere('p.price >= :minprice')
                        ->setParameter('minprice', $search->getMinPrice());
        }

        return $query->getQuery()->getResult();
    }

    /**
     * @return Product[] 
     * Returns an array of Product objects filtered
     */
    public function filterSoldProduct(SearchProduct $search)
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('c', 'p')
            ->join('p.category', 'c')
            ->andWhere('p.isSold = 1');


        if(!empty($search->string)){
            $query = $query
                ->andWhere('p.name LIKE :string')
                ->setParameter('string', "%{$search->string}%");
        }

        if(!empty($search->categories)){
            $query = $query
                        ->andWhere('c.id IN (:categories)')
                        ->setParameter('categories', $search->categories);
        }

        return $query->getQuery()->getResult();
    }
}
