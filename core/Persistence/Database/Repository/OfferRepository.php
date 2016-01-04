<?php
namespace Core\Persistence\Database\Repository;

use \Core\Domain\Repository\OfferRepositoryInterface;
use Core\Domain\Entity\OfferDTO;

class OfferRepository extends BaseRepository implements OfferRepositoryInterface
{
    public function getOfferDTO($entity, $conditions) {
        $sql = "SELECT NEW OfferDTO(o.title) FROM $entity o where o.id>0 ";
        if (!empty($conditions)) {
            foreach ($conditions as $key=>$value) {
                $sql .= " and o.". $key . "=". $value;
            }
        }
        $query = $this->em->createQuery($sql);
        $offers = $query->getResult();
        return $offers[0];
    }
}
