<?php

namespace Core\Domain\Repository;

interface NewsletterCampaignRepositoryInterface extends BaseRepositoryInterface
{
    public function findByConditions($entity, $conditions, $order, $limit, $offset);
}
