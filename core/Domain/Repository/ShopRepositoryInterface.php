<?php

namespace Core\Domain\Repository;

interface ShopRepositoryInterface extends BaseRepositoryInterface
{
    public function persist($entity);
}
