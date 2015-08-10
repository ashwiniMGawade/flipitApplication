<?php
namespace Core\Persistence\Database\Repository;

use Core\Domain\Repository\ViewCountRepositoryInterface;

class ViewCountRepository extends BaseRepository implements ViewCountRepositoryInterface
{
    protected $entity = '\Core\Domain\Entity\Visitor';

    public function getOfferClickCount($offerId, $clientIp)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $offerClick = $queryBuilder
            ->select('count(v.id) as countExists')
            ->addSelect("(SELECT  click.id FROM \Core\Domain\Entity\ViewCount click WHERE click.id = v.id) as clickId")
            ->from('\Core\Domain\Entity\ViewCount', 'v')
            ->where('v.onClick!=0')
            ->andWhere('v.viewcount='.$offerId)
            ->andWhere('v.IP='.$clientIp)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $offerClick[0]['countExists'];
    }
}
