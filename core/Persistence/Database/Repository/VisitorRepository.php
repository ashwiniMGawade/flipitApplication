<?php
namespace Core\Persistence\Database\Repository;

use Core\Domain\Repository\VisitorRepositoryInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

class VisitorRepository extends BaseRepository implements VisitorRepositoryInterface
{
    protected $entity = '\Core\Domain\Entity\Visitor';

    public function findVisitors($conditions, $options)
    {
        $limit = isset($options['limit']) && $options['limit'] !== null ? $options['limit'] : 100;
        $offset = isset($options['offset']) && $options['offset'] !== null ? $options['offset'] : 0;
        $sortColumn = isset($options['sortByColumn']) && $options['sortByColumn'] !== null ? $options['sortByColumn'] : 'id';
        $sortDirection = isset($options['sortDirection']) && $options['sortDirection'] !== null ? $options['sortDirection'] : 'DESC';

        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('v')
            ->from($this->entity, 'v')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy("v.$sortColumn", $sortDirection);

        if (isset($conditions['deleted'])) {
            $queryBuilder->where("v.deleted=".$conditions['deleted']."");
        }
        if (isset($conditions['firstName']) && !empty($conditions['firstName'])) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->like('v.firstName', $queryBuilder->expr()->literal($conditions['firstName'].'%'))
            );
        }
        if (isset($conditions['email']) && !empty($conditions['email'])) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->like('v.email', $queryBuilder->expr()->literal($conditions['email'].'%'))
            );
        }
        $query = $queryBuilder->getQuery();

        $results['visitors'] = $query->getResult();
        $paginator = new Paginator($query, $fetchJoinCollection = true);
        $results['visitorCount'] = count($paginator);
        return  $results;
    }

    public function deactivate($filters)
    {
        $currentTime = date('Y-m-d H:i:s');
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->update($this->entity, 'v')
            ->set('v.active', 0)
            ->set('v.updated_at', "'$currentTime'")
            ->where('v.active = 1')
            ->andWhere('v.deleted = 0');

        if (isset($filters['lastEmailOpenDate']) && !empty($filters['lastEmailOpenDate'])) {
            $fieldValue = $filters['lastEmailOpenDate'];
            $queryBuilder->andWhere("v.lastEmailOpenDate < '$fieldValue'");
        }
        $query = $queryBuilder->getQuery();
        $response = $query->execute();
        return $response;
    }
}
