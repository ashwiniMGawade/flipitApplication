<?php
namespace KC\Repository;
class CodeAlertQueue Extends \KC\Entity\CodeAlertQueue
{
    public static function saveCodeAlertQueue($shopId, $offerId)
    {
        $codeAlertQueueValue = 0;
        if (isset($shopId) && $shopId != '') {
            $shop = \KC\Repository\FavoriteShop::getShopsById($shopId);
            if (!empty($shop)) {
                $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $query = $queryBuilder
                ->select('c')
                ->from("KC\Entity\CodeAlertQueue", 'c')
                ->where($queryBuilder->expr()->eq('c.offerId', $queryBuilder->expr()->literal($offerId)))
                ->andWhere('c.deleted = 0');
                $codeAlertInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

                if (empty($codeAlertInformation)) {
                    $entityManagerLocale  = \Zend_Registry::get('emLocale');
                    $codeAlertQueue = new \KC\Entity\CodeAlertQueue();
                    $codeAlertQueue->offerId = $offerId;
                    $codeAlertQueue->shopId = $shopId;
                    $codeAlertQueue->deleted = 0;
                    $codeAlertQueue->created_at = new \DateTime('now');
                    $codeAlertQueue->updated_at = new \DateTime('now');
                    $entityManagerLocale->persist($codeAlertQueue);
                    $entityManagerLocale->flush();
                    $codeAlertQueueValue = 1;
                }
            } else {
                $codeAlertQueueValue = 2;
            }
        }
        return $codeAlertQueueValue;
    }

    public static function getRecepientsCount()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('c.shopId')
        ->from('KC\Entity\CodeAlertQueue', 'c');
        $codeAlertShopIds = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $visitorsCount = 0;
        foreach ($codeAlertShopIds as $codeAlertShopId) {
            $queryBuilderfavouriteShopCount = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilderfavouriteShopCount
                ->select('count(fs.id)')
                ->from('KC\Entity\FavoriteShop', 'fs')
                ->where('fs.shopId = '.$codeAlertShopId['shopId']);
            $favouriteShopCount = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            foreach ($favouriteShopCount as $favouriteShopCountValue) {
                $visitorsCount += $favouriteShopCountValue['count'];
            }
        }
        return $visitorsCount;
    }

    public static function moveCodeAlertToTrash($codeAlertId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->delete('KC\Entity\CodeAlertQueue', 'c')
            ->where('c.offerId ='.$codeAlertId)
            ->getQuery();
        $query->execute();
        return true;
    }

    public static function getCodealertOffers()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('c.offerId,c.shopId')
            ->from('KC\Entity\CodeAlertQueue', 'c')
            ->where('c.deleted = 0');
        $codeAlertOfferIds = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $offers =  array();
        $codeAlertOffers = array();
        foreach ($codeAlertOfferIds as $codeAlertOfferId) {
            $shop = \KC\Repository\FavoriteShop::getShopsById($codeAlertOfferId['shopId']);
            if (!empty($shop)) {
                foreach (\KC\Repository\Offer::getOfferDetail($codeAlertOfferId['offerId'], 'codealert') as $codeAlertOfferValue) {
                    $offers = $codeAlertOfferValue;
                    $offers['shop']['visitors'] = $shop;
                    $codeAlertOffers[] = $offers;
                }
            }
        }
        return $codeAlertOffers;
    }

    public static function getCodeAlertList($codeAlertParameters, $sentCodes = '')
    {
        $searchText = isset($codeAlertParameters["SearchText"]) && $codeAlertParameters["SearchText"] != 'undefined'
            ? $codeAlertParameters["SearchText"] : '';
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('c')
        ->from("KC\Entity\CodeAlertQueue", "c")
        ->where($queryBuilder->expr()->like('c.offerId', $queryBuilder->expr()->literal($searchText.'%')))
        ->orderBy("c.id", "DESC");
        $deletedStatus = isset($sentCodes) && $sentCodes != '' ? 1 : 0;
        $query =  $query->andWhere('c.deleted = '.$deletedStatus);
        $codeAlertOfferIds = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $codeAlertOffersId = array();

        if (!empty($codeAlertOfferIds)) {
            foreach ($codeAlertOfferIds as $codeAlertOfferId) {
                $shop = \KC\Repository\FavoriteShop::getShopsById($codeAlertOfferId['shopId']);
                if (!empty($shop)) {
                    $codeAlertOffersId[] = $codeAlertOfferId['offerId'];
                }
            }
        }
        
        $offerDetails = array();
        if (!empty($codeAlertOffersId)) {
            $offerIds = implode(',', $codeAlertOffersId);
        } else {
            $offerIds = '';
        }

        if (!empty($offerIds)) {
            $queryBuilderOffer = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $offerDetails = $queryBuilderOffer
                ->from("KC\Entity\Offer", "o")
                 ->leftJoin('o.shopOffers', 's')
                ->addSelect(
                    "(SELECT count(fs.id) FROM KC\Entity\FavoriteShop fs LEFT JOIN fs.visitor vs 
                    WHERE fs.shop = s.id AND vs.id = fs.visitor AND vs.codeAlert = 1) as visitors"
                )
                ->where("o.userGenerated = 0");
            if (!empty($offerIds)) {
                $offerDetails->andWhere($queryBuilderOffer->expr()->in('o.id', $offerIds));
            }
        }
        $request  = \DataTable_Helper::createSearchRequest(
            $codeAlertParameters,
            array()
        );
        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder
        ->setQueryBuilder($offerDetails)
        ->add('number', 'o.id')
        ->add('text', 's.name')
        ->add('text', 'o.title');
        $codeAlertList = $builder->getTable()->getResponseArray();
        return $codeAlertList;
    }

    public static function clearCodeAlertQueueByOfferId($offerId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $queryBuilder->update('KC\Entity\CodeAlertQueue', 'c')
            ->set('c.deleted', 1)
            ->where("c.offerId=".$offerId)
            ->getQuery()->execute();
        return true;
    }
}