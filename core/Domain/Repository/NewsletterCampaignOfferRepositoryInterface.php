<?php

namespace Core\Domain\Repository;

interface NewsletterCampaignOfferRepositoryInterface extends BaseRepositoryInterface
{
    public function findNewsletterCampaignOffers($conditions, $order, $limit, $offset);
    public function deleteNewsletterCampaignOffers($offerIds);
    public function addNewsletterCampaignOffer($offer);
}
