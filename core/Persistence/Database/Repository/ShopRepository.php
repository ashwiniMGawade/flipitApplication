<?php

namespace Core\Persistence\Database\Repository;

use \Core\Domain\Repository\ShopRepositoryInterface;
use \Core\Domain\Entity\Shop;

class ShopRepository extends BaseRepository implements ShopRepositoryInterface
{
    public function persist($entity)
    {
        $this->em->merge($entity);
        $this->em->flush();
        return $entity;
    }
}
