<?php
class Zend_Controller_Action_Helper_Index extends Zend_Controller_Action_Helper_Abstract
{
    public static function getSpecialListPagesOffers($specialListPages)
    {
        $specialOfferslist = '';
        foreach ($specialListPages as $specialListPage) {
            foreach ($specialListPage['page'] as $page) {
                $specialOfferslistIndex = $page['permaLink'] . ',' . $page['pageTitle'];
                $specialOfferslist[$specialOfferslistIndex] = self::removeDuplicateCode(Offer::getSpecialPageOffers($page));
                
            }
        }
        return $specialOfferslist;
    }

    public static function removeDuplicateCode($offers)
    {
        $offersWithoughtDuplicateShop = '';
        foreach ($offers as $offerId => $offer) {
            $offersWithoughtDuplicateShop[$offer['shop']['id']] = $offers[$offerId];
        }
        return $offersWithoughtDuplicateShop;
    }
}
