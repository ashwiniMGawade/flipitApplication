<?php
namespace Core\Domain\Repository;

interface OfferRepositoryInterface extends BaseRepositoryInterface
{
    public function getOfferDTO($entity, $conditions);
}
