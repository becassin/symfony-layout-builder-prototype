<?php

namespace App\Repository;

use App\Entity\LayoutBlock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LayoutBlock>
 *
 * @method LayoutBlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method LayoutBlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method LayoutBlock[]    findAll()
 * @method LayoutBlock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LayoutBlockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LayoutBlock::class);
    }

    public function save(LayoutBlock $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LayoutBlock $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
