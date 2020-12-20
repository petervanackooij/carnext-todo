<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Task;

/**
 * Class TaskRepository.
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Task[] findAll()
 */
class TaskRepository extends ServiceEntityRepository
{
    /**
     * TaskRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @return Task[]
     */
    public function findActiveTasks(): array
    {
        $qb = $this->createQueryBuilder('task');

        $qb->where($qb->expr()->isNull('task.completedAt'));

        return $qb->getQuery()->getResult();
    }
}
