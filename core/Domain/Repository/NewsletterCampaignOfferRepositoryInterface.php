<?php

namespace Core\Domain\Repository;

interface NewsletterCampaignOfferRepositoryInterface extends BaseRepositoryInterface
{
    public function findNewsletterCampaignOffers($conditions);
    public function deleteNewsletterCampaignOffers($offerIds);
}
