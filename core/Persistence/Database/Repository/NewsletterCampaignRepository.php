<?php
namespace Core\Persistence\Database\Repository;

use Core\Domain\Repository\NewsletterCampaignRepositoryInterface;

class NewsletterCampaignRepository extends BaseRepository implements NewsletterCampaignRepositoryInterface
{
    public function findByConditions($entity, $conditions, $order, $limit, $offset)
    {
        if (empty($conditions)) {
            return false;
        }
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('table')
            ->from($entity, 'table')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if (false === empty($order)) {
            $orderField = key($order);
            $queryBuilder->orderBy("table.$orderField", $order[$orderField]);
        }
        $conditionsCount=1;
        foreach ($conditions as $field => $value) {
            if ($conditionsCount === 1) {
                $queryBuilder->where("table.$value[0]$value[1]".$queryBuilder->expr()->literal($value[2]));
            } else {
                $queryBuilder->andWhere("table.$value[0]$value[1]".$queryBuilder->expr()->literal($value[2]));
            }
            $conditionsCount++;
        }
        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }
}
