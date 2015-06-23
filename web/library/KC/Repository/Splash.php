<?php
namespace KC\Repository;

class Splash extends \Core\Domain\Entity\User\Splash
{
    public function getSplashInformation()
    {
        $queryBuilder  = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder
            ->select('s')
            ->from('\KC\Entity\User\Splash', 's');
        $splashInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $splashInformation;
    }

    public function deleteSplashoffer()
    {
        $queryBuilder  = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->delete('\KC\Entity\User\Splash', 's')->getQuery()->execute();
        return true;
    }

    public function getOfferById($offerId)
    {
        $offer = \Zend_Registry::get('emLocale')->find('\KC\Entity\Offer', $offerId);
        return $offer;
    }

    public function saveSplashOffer($offerId, $locale)
    {
        $splashObject = new \KC\Entity\User\Splash();
        $splashObject->id = 1;
        $splashObject->offerId = $offerId;
        $splashObject->locale = $locale;
        $splashObject->deleted = 0;
        $splashObject->created_at = new \DateTime('now');
        $splashObject->updated_at = new \DateTime('now');
        \Zend_Registry::get('emUser')->persist($splashObject);
        \Zend_Registry::get('emUser')->flush();
        return true;
    }
}
