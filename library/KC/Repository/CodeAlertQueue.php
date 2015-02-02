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
                ->where('c.offerId = '.$offerId)
                ->andWhere('c.deleted = 0');
                $codeAlertInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

                if (empty($codeAlertInformation)) {
                    $entityManagerLocale  = \Zend_Registry::get('emLocale');
                    $codeAlertQueue = new \KC\Entity\CodeAlertQueue();
                    $codeAlertQueue->offerId = $offerId;
                    $codeAlertQueue->shopId = $shopId;
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
            ->where('c.id ='.$codeAlertId)
            ->getQuery();
        $query->execute();
        return true;
    }

    public static function getCodealertOffers()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('c.offerId,c.shopId')
            ->from('KC\Entity\CodeAlertQueue', 'c');
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
        ->where("c.offerId LIKE '$searchText%'")
        ->orderBy("c.id", "DESC");
        $deletedStatus = isset($sentCodes) && $sentCodes != '' ? 1 : 0;
        $query =  $query->andWhere('c.deleted = '.$deletedStatus);
        $codeAlertOfferIds = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $codeAlertOffersId = array();
        foreach ($codeAlertOfferIds as $codeAlertOfferId) {
            $shop = \KC\Repository\FavoriteShop::getShopsById($codeAlertOfferId['shopId']);
            if (!empty($shop)) {
                $codeAlertOffersId[] = $codeAlertOfferId['offerId'];
            }
        }
        $offerIds = implode(',', $codeAlertOffersId);
        $offerDetails = array();
        if (!empty($offerIds)) {
            $queryBuilderOffer = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $offerDetails = $queryBuilderOffer
                ->select('o.id,o.title,s.name')
                ->from("KC\Entity\Offer", "o")
                ->leftJoin('o.shopOffers', 's')
                ->leftJoin('s.affliatenetwork', 'a')
                ->leftJoin('o.offers', 'p')
                ->leftJoin('o.offertermandcondition', 'tc')
                ->leftJoin('o.categoryoffres', 'cat')
                ->leftJoin('o.logo', 'img')
                ->leftJoin('s.offerNews', 'news')
                ->leftJoin('o.offerTiles', 't')
                ->addSelect(
                    "(SELECT count(fs.id) FROM KC\Entity\FavoriteShop fs LEFT JOIN fs.visitor vs 
                    WHERE fs.shop = s.id AND vs.id = fs.visitor AND vs.codealert = 1) as visitors"
                )
                ->addSelect("(SELECT cq.id FROM KC\Entity\CodeAlertQueue cq WHERE cq.offerId = o.id) as codeAlertId")
                ->andWhere("o.id IN($offerIds)")
                ->andWhere("o.userGenerated = '0'");
        }

        $request  = \DataTable_Helper::createSearchRequest(
            $codeAlertParameters,
            array('o.id', 's.name','o.title','visitors','codeAlertId')
        );

        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder
        ->setQueryBuilder($offerDetails)
        ->add('text', 's.name')
        ->add('text', 'o.title')
        ->add('number', 'visitors')
        ->add('number', 'codeAlertId');
        $codeAlertList = $builder->getTable()->getResultQueryBuilder()->getQuery()->getArrayResult();
        $codeAlertList = \DataTable_Helper::getResponse($codeAlertList, $request);
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