<?php
namespace Core\Persistence\Database\Repository;

use Core\Domain\Repository\VisitorRepositoryInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

class VisitorRepository extends BaseRepository implements VisitorRepositoryInterface
{
    protected $entity = '\Core\Domain\Entity\Visitor';

    public function deactivateSleeper($filters)
    {
        $currentTime = date('Y-m-d H:i:s');
        $inactiveStateReason = 'Sleeper';
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->update($this->entity, 'v')
            ->set('v.active', 0)
            ->set('v.updated_at', "'$currentTime'")
            ->set('v.inactiveStatusReason', "'$inactiveStateReason'")
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

    public function getNewsletterReceipientCount()
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('count(v.id) as recepients')
            ->from('\Core\Domain\Entity\Visitor', 'v')
            ->where('v.status = 1')
            ->andWhere('v.active = 1')
            ->andWhere('v.weeklyNewsLetter = 1');
        $response = $queryBuilder->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $response;
    }
}
