<?php
class Zend_Controller_Action_Helper_Store extends Zend_Controller_Action_Helper_Abstract
{
    public static function topStorePopularOffers($shopId, $offers)
    {
        $voucherCacheKeyCheck =
            FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_popularvouchercode_list_shoppage');
        $shopCategories = \KC\Repository\Shop::returnShopCategories($shopId);
        if ($voucherCacheKeyCheck) {
            $shopCategories = \KC\Repository\Shop::returnShopCategories($shopId);
            FrontEnd_Helper_viewHelper::setInCache('allCategoriesOf_shoppage_'. $shopId, $shopCategories);
            $topVoucherCodes = \KC\Repository\Offer::getTopCouponCodes($shopCategories, 100);
            FrontEnd_Helper_viewHelper::setInCache('all_popularvouchercode_list_shoppage', $topVoucherCodes);
        } else {
            $shopCategories = FrontEnd_Helper_viewHelper::getFromCacheByKey('allCategoriesOf_shoppage_'. $shopId);
            $topVoucherCodes = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_popularvouchercode_list_shoppage');
        }
        $offers = array();
        $storeOfferIds = array();
        foreach ($topVoucherCodes as $topVouchercodeskey => $topVoucherCode) {
            $offers[] = $topVoucherCode['offer'];
        }
        return $offers;
    }

    public static function removeDuplicateShopsOffers($similarShopsOffers)
    {
        $removeDuplicateShop = '';
        foreach ($similarShopsOffers as $offerIndex => $offer) {
            $removeDuplicateShop[$offer['shopOffers']['id']] = $similarShopsOffers[$offerIndex];
        }
        $offersUnique = '';
        foreach ($removeDuplicateShop as $shopIndex => $offer) {
            $offersUnique[] = $removeDuplicateShop[$shopIndex];
        }
        return  $offersUnique;
    }

    public static function changeIndexOfSixReasons($sixShopReasons)
    {
        $sixShopReasonsWithIndex = '';
        foreach ($sixShopReasons as $reason) {
            $sixShopReasonsWithIndex[$reason['fieldname']] = $reason['fieldvalue'];
        }
        if (!empty($sixShopReasonsWithIndex)) {
            $sixShopReasonsWithIndex = array_chunk($sixShopReasonsWithIndex, 2);
        }
        return  $sixShopReasonsWithIndex;
    }

    public static function getActualPermalink($fullPermalink, $peramType)
    {
        $splittedPermalink = explode('-', $fullPermalink);
        switch ($peramType)
        {
            case 'permalink':
                    $countOfPermalinkStringValues = $splittedPermalink;
                    if (array_key_exists(count($countOfPermalinkStringValues) - 2, $splittedPermalink)) {
                        unset($splittedPermalink[count($countOfPermalinkStringValues) - 2]);
                    }  
                    if (array_key_exists(count($countOfPermalinkStringValues) - 1, $splittedPermalink)) {
                        unset($splittedPermalink[count($countOfPermalinkStringValues) - 1]);
                    }
                    $urlString = implode("-", $splittedPermalink);
                break;

            case 'firstCharacter':
                $urlString = $splittedPermalink[count($splittedPermalink) - 2];
                break;

            case 'lastCharacter':
                $urlString = $splittedPermalink[count($splittedPermalink) - 1];
                break;

            default:
                $urlString = $splittedPermalink;
                break;
        }
        return $urlString;
    }

    public static function getHowToGuide($shopId)
    {
        $cacheKey = FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter($shopId);
        $howToGuides = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'store_'.$cacheKey.'_howToGuide',
            array('function' => 'KC\Repository\Shop::getShopDetails', 'parameters' => array($shopId))
        );
        return $howToGuides;
    }

    public static function getShopInformation($shopId, $shopList)
    {
        $allShopDetailKey = 'shopDetails_'.$shopList;
        $shopInformation = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            $allShopDetailKey,
            array('function' => 'KC\Repository\Shop::getStoreDetails', 'parameters' => array($shopId))
        );
        return $shopInformation;
    }

    public static function getShopLatestUpdates($shopId, $shopList)
    {
        $allLatestUpdatesInStoreKey = 'ShoplatestUpdates_'.$shopList;
        $latestShopUpdates = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            $allLatestUpdatesInStoreKey,
            array('function' => 'FrontEnd_Helper_viewHelper::getShopCouponCode', 'parameters' => array(
                'latestupdates',
                4,
                $shopId)
            )
        );
        return $latestShopUpdates;
    }

    public static function getSixTopOffers($shopId, $shopList)
    {
        $allOffersInStoreKey = '6_topOffersHowto'.$shopList;
        $offers = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            $allOffersInStoreKey,
            array('function' => 'FrontEnd_Helper_viewHelper::commonfrontendGetCode',
                'parameters' => array('topSixOffers', 3, $shopId, 0)
            )
        );
        $offers = array_chunk($offers, 3);
        return $offers;
    }

    public static function getShopChain($shopInformation)
    {
        $shopChain = '';
        if (!empty($shopInformation[0]['showChains'])) {
            $frontEndViewHelper = new FrontEnd_Helper_SidebarWidgetFunctions();
            $shopChains = $frontEndViewHelper->sidebarChainWidget(
                $shopInformation[0]['id'],
                $shopInformation[0]['name'],
                $shopInformation[0]['chainItemId']
            );
            if ($shopChains['hasShops'] && isset($shopChains['string'])) {
                $shopChain = $shopChains['string'];
            }
        }
        return $shopChain;
    }

    public static function getNumberOfDaysTillOfferGetsLive($offerInfo)
    {
        $daysLeftTillOfferGetsLive = '';
        if (!empty($offerInfo) && isset($offerInfo[0]['startDate'])) {
            $offerStartDate = $offerInfo[0]['startDate']->format('Y-m-d');
            $secondsForStartDate = strtotime($offerStartDate) - time();
            $daysLeftTillOfferGetsLive = floor($secondsForStartDate / 86400);
            if ($daysLeftTillOfferGetsLive <= 5) {
                $daysLeftTillOfferGetsLive = isset($daysLeftTillOfferGetsLive) && $daysLeftTillOfferGetsLive > 1
                    ? $daysLeftTillOfferGetsLive . ' ' . FrontEnd_Helper_viewHelper::__translate('days')
                    : $daysLeftTillOfferGetsLive . ' ' . FrontEnd_Helper_viewHelper::__translate('day');
            }
        }
        return $daysLeftTillOfferGetsLive;
    }
}
