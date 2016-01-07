<?php
namespace Core\Persistence\Database\Repository;

use Core\Domain\Repository\NewsletterCampaignOfferRepositoryInterface;

class NewsletterCampaignOfferRepository extends BaseRepository implements NewsletterCampaignOfferRepositoryInterface
{
    public function findNewsletterCampaignOffers($conditions)
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('newsletterCampaignOffer', 'offer.id', 'offer.startDate', 'offer.title')
            ->from('\Core\Domain\Entity\NewsletterCampaignOffer', 'newsletterCampaignOffer')
            ->join('newsletterCampaignOffer.offer', 'offer');

        $conditionsCount = 1;
        foreach ($conditions as $field => $value) {
            if ($conditionsCount === 1) {
                $queryBuilder->where("newsletterCampaignOffer.$field='$value'");
            } else {
                $queryBuilder->andWhere("newsletterCampaignOffer.$field='$value'");
            }
            $conditionsCount++;
        }

        $query = $queryBuilder->getQuery();
        $result = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $result;
    }

    public function deleteNewsletterCampaignOffers($offerIds)
    {
        $queryBuilder = $this->em->createQueryBuilder();

        $queryBuilder->delete('\Core\Domain\Entity\NewsletterCampaignOffer', 'o')
        ->where($queryBuilder->expr()->In('o.id', $offerIds))
        ->getQuery()
        ->execute();
    }


    public function addNewsletterCampaignOffer($offer)
    {

    }
}
