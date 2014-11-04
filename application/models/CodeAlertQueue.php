<?php

class CodeAlertQueue extends BaseCodeAlertQueue
{

    public static function saveCodeAlertQueue($shopId, $offerId)
    {
        $codeAlertQueueValue = 0;
        if (isset($shopId) && $shopId != '') {
            $shop = FavoriteShop::getShopsById($shopId);
            if (!empty($shop)) {
                $codeAlertInformation = Doctrine_Query::create()
                    ->select("*")
                    ->from("CodeAlertQueue")
                    ->where('offerId = '.$offerId)
                    ->fetchArray();

                if (empty($codeAlertInformation)) {
                    $codeAlertQueue = new CodeAlertQueue();
                    $codeAlertQueue->offerId = $offerId;
                    $codeAlertQueue->shopId = $shopId;
                    $codeAlertQueue->save();
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
        $codeAlertShopIds = Doctrine_Query::create()
        ->select('c.shopId')
        ->from('CodeAlertQueue c')
        ->fetchArray();

        $visitorsCount = 0;
        foreach ($codeAlertShopIds as $codeAlertShopId) {
            $favouriteShopCount = Doctrine_Query::create()
                ->select('count(fs.id)')
                ->from('FavoriteShop fs')
                ->where('shopId = '.$codeAlertShopId['shopId'])
                ->fetchArray();

            foreach ($favouriteShopCount as $favouriteShopCountValue) {
                $visitorsCount += $favouriteShopCountValue['count'];
            }
        }
        return $visitorsCount;
    }

    public static function moveCodeAlertToTrash($codeAlertId)
    {
        Doctrine_Query::create()->delete()->from('CodeAlertQueue c')->where("c.id=".$codeAlertId)->execute();
        return true;
    }

    public static function getCodealertOffers()
    {
        $codeAlertOfferIds = Doctrine_Query::create()
            ->select('c.offerId,c.shopId')
            ->from('CodeAlertQueue c')
            ->fetchArray();
        $offers =  array();
        $codeAlertOffers = array();
        foreach ($codeAlertOfferIds as $codeAlertOfferId) {
            $shop = FavoriteShop::getShopsById($codeAlertOfferId['shopId']);
            if (!empty($shop)) {
                foreach (Offer::getOfferDetail($codeAlertOfferId['offerId'], 'codealert') as $codeAlertOfferValue) {
                    $offers = $codeAlertOfferValue;
                    $offers['shop']['visitors'] = $shop;
                    $codeAlertOffers[] = $offers;
                }
            }
        }
        return $codeAlertOffers;
    }

    public static function getCodeAlertList($codeAlertParameters)
    {
        $searchText = isset($codeAlertParameters["SearchText"]) && $codeAlertParameters["SearchText"] != 'undefined'
            ? $codeAlertParameters["SearchText"] : '';
        $codeAlertOfferIds = Doctrine_Query::create()
        ->select('c.*')
        ->from("CodeAlertQueue c")
        ->andWhere("c.offerId LIKE ?", "$searchText%")
        ->orderBy("c.id DESC")->fetchArray();
        $codeAlertOffersId = array();
        foreach ($codeAlertOfferIds as $codeAlertOfferId) {
            $shop = FavoriteShop::getShopsById($codeAlertOfferId['shopId']);
            if (!empty($shop)) {
                $codeAlertOffersId[] = $codeAlertOfferId['offerId'];
            }
        }
        $offerIds = implode(',', $codeAlertOffersId);
        $offerDetails = array();
        if (!empty($offerIds)) {
            $offerDetails = Doctrine_Query::create()
                ->select('o.id,o.title,s.name')
                ->from("Offer o")
                ->leftJoin('o.shop s')
                ->leftJoin('s.affliatenetwork a')
                ->leftJoin('o.page p')
                ->leftJoin('o.termandcondition tc')
                ->leftJoin('o.category cat')
                ->leftJoin('o.logo img')
                ->leftJoin('o.offernews news')
                ->leftJoin('o.tiles t')
                ->addSelect("(SELECT count(fs.id) FROM FavoriteShop fs WHERE fs.shopId = s.id) as visitors")
                ->addSelect("(SELECT cq.id FROM CodeAlertQueue cq WHERE cq.offerId = o.id) as codeAlertId")
                ->andWhere("o.id IN($offerIds)")
                ->andWhere("o.userGenerated = '0'");
        }
        $codeAlertList = DataTable_Helper::generateDataTableResponse(
            $offerDetails,
            $codeAlertParameters,
            array(
                "__identifier" => 'o.id', 's.name','o.title','visitors','codeAlertId'
            ),
            array(),
            array()
        );
 
        return $codeAlertList;
    }

    public static function clearCodeAlertQueueByOfferId($offerId)
    {
        Doctrine_Query::create()->delete()->from('CodeAlertQueue c')->where("c.offerId=".$offerId)->execute();
        return true;
    }
}
