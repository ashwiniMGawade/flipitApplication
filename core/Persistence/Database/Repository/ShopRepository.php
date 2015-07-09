<?php

namespace Core\Persistence\Database\Repository;

use \Core\Domain\Repository\ShopRepositoryInterface;
use \Core\Domain\Entity\Shop;

class ShopRepository extends BaseRepository implements ShopRepositoryInterface
{
    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }
}
