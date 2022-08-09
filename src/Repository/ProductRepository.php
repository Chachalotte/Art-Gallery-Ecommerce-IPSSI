<?php

namespace App\Repository;

use App\Entity\Product;
use App\Data\SearchProduct;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Product::class);        
        $this->paginator = $paginator;
    }

    /**
     * @return PaginationInterface
     * Returns an array of Product objects filtered
     */
    public function filterProduct(SearchProduct $search): PaginationInterface
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

        $query = $query->getQuery();
        return $this->paginator->paginate(
            $query,
            $search->page,
            25
        );
    }

    /**
     * @return PaginationInterface
     * Returns an array of Product objects filtered
     */
    public function filterSoldProduct(SearchProduct $search): PaginationInterface
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

        $query = $query->getQuery();
        return $this->paginator->paginate(
            $query,
            $search->page,
            25
        );
    }
}
