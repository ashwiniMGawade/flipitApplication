<?php

/**
 * Code alert queue
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
class CodeAlertQueue extends BaseCodeAlertQueue
{

    public static function saveCodeAlertQueue($shopId, $offerId)
    {
        if (isset($shopId) && $shopId != '') {
            $getRecord = Doctrine_Query::create()
                ->select()
                ->from("CodeAlertQueue")
                ->where('offerId = '.$offerId)
                ->fetchArray();

            if (empty($getRecord)) {
                $codeAlertQueue = new CodeAlertQueue();
                $codeAlertQueue->offerId = $offerId;
                $codeAlertQueue->shopId = $shopId;
                $codeAlertQueue->save();
            }
        }
        return true;
    }

    public static function getRecepientsCount()
    {
        $codeAlertShopIds = Doctrine_Query::create()
        ->select('c.shopId')
        ->from('CodeAlertQueue c')
        ->fetchArray();

        $visitorsCount = 0;
        foreach ($codeAlertShopIds as $codeAlertShopId) {
            $count = Doctrine_Query::create()
                ->select('count(fs.id)')
                ->from('FavoriteShop fs')
                ->where('shopId = '.$codeAlertShopId['shopId'])
                ->fetchArray();

            foreach ($count as $countValue) {
                $visitorsCount += $countValue['count'];
                
            }
        }
        return $visitorsCount;
    }

    public static function getCodealertOffers()
    {
        $codeAlertOfferIds = Doctrine_Query::create()
        ->select('c.offerId,c.shopId')
        ->from('CodeAlertQueue c')
        ->fetchArray();
        $offers =  array();
        $visitorsCount = 0;
        foreach ($codeAlertOfferIds as $codeAlertOfferId) {
            $shop = FavoriteShop::getShopsById($codeAlertOfferId['shopId']);
           //  echo "<pre>"; print_r($shop);
            if(!empty($shop)) {
               // $offers[] = Offer::getOfferDetail($codeAlertOfferId['offerId']);
               
               foreach (Offer::getOfferDetail($codeAlertOfferId['offerId']) as $key => $value) {
                $offers = $value;
                $offers['shop']['visitors'] = $shop;
                $ooo[] = $offers;
               }
            }
            
            
        } 

 
        return $ooo;
    }

    public static function getCodeAlertList($params)
    {
        $srh = @$params["SearchText"] != 'undefined' ? @$params["SearchText"] : '';
        $codeAlertOfferIds = Doctrine_Query::create()
        ->select('c.*')
        ->from("CodeAlertQueue c")
        ->andWhere("c.offerId LIKE ?", "$srh%")
        ->orderBy("c.id DESC")->fetchArray();

        foreach ($codeAlertOfferIds as $codeAlertOfferId) {
            $shop = FavoriteShop::getShopsById($codeAlertOfferId['shopId']);
            if (!empty($shop)) {
                $test[] = $codeAlertOfferId['offerId'];
            }
        }
        $offerId = implode(',', $test);
 
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
            ->andWhere("o.id IN($offerId)")
            ->andWhere("o.userGenerated = '0'");
 
         $list = DataTable_Helper::generateDataTableResponse(
            $offerDetails,
            $params,
            array(
                "__identifier" => 'o.id', 's.name','o.title','visitors'
            ),
            array(),
            array()
        );

        return $list;
    }

    public static function clearCodeAlertQueueByOfferId($offerId)
    {
        Doctrine_Query::create()->delete()->from('CodeAlertQueue c')->where("c.offerId=".$offerId)->execute();
        return true;
    }
}
