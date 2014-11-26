<?php

class Splash extends BaseSplash
{
    public function getSplashInformation()
    {
        $queryBuilder  = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder
            ->select('s')
            ->from('\KC\Entity\Splash', 's');
        $splashInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $splashInformation;
    }

    public function deleteSplashoffer()
    {
        $queryBuilder  = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->delete('\KC\Entity\Splash', 's')->getQuery()->execute();
        return true;
    }

    public function getOfferById($offerId)
    {
        $offer = \Zend_Registry::get('emLocale')->find('\KC\Entity\Offer', $offerId);
        return $offer;
    }
}
