<?php
namespace Core\Domain\Repository;

interface ViewCountRepositoryInterface extends BaseRepositoryInterface
{
    public function getOfferClickCount($offerId, $clientIp);
}
