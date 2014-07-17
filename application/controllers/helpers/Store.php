<?php
class Zend_Controller_Action_Helper_Store extends Zend_Controller_Action_Helper_Abstract
{
    public static function topStorePopularOffers($shopId, $offers)
    {
        $voucherCacheKeyCheck =
            FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_popularvouchercode_list_shoppage');
        $shopCategories = Shop::returnShopCategories($shopId);
        if ($voucherCacheKeyCheck) {
            $shopCategories = Shop::returnShopCategories($shopId);
            FrontEnd_Helper_viewHelper::setInCache('all_categories_of_shoppage_'. $shopId, $shopCategories);
            $topVoucherCodes = Offer::getTopCouponCodes($shopCategories, 100);
            FrontEnd_Helper_viewHelper::setInCache('all_popularvouchercode_list_shoppage', $topVoucherCodes);
        } else {
            $shopCategories = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_categories_of_shoppage_'. $shopId);
            $topVoucherCodes = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_popularvouchercode_list_shoppage');
        }
        $offers = array();
        $storeOfferIds = array();
        foreach ($topVoucherCodes as $topVouchercodeskey => $topVoucherCode) {
            $offers[] = $topVoucherCode['offer'];
        }
        return $offers;
    }
}