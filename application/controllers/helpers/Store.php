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
            $removeDuplicateShop[$offer['shop']['id']] = $similarShopsOffers[$offerIndex];
        }
        $offersUnique = '';
        foreach ($removeDuplicateShop as $shopIndex => $offer) {
            $offersUnique[] = $removeDuplicateShop[$shopIndex];
        }
        return  $offersUnique;
    }
}
