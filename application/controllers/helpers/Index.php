<?php
class Zend_Controller_Action_Helper_Index extends Zend_Controller_Action_Helper_Abstract
{
    public static function getSpecialListPagesOffers($specialListPages)
    {
        $specialOfferslist = '';
        foreach ($specialListPages as $specialListPage) {
            foreach ($specialListPage[0]['page'] as $page) {
                $specialOfferslistIndex = $page['permaLink'] . ',' . $page['pageTitle'];
                $specialOfferslist[$specialOfferslistIndex] = self::removeDuplicateCode(\KC\Repository\Offer::getSpecialPageOffers($page));
             
   
            }
        }
        return $specialOfferslist;
    }

    public static function removeDuplicateCode($offers, $pageName = '')
    {
        $offersWithoutDuplicateShop = '';
        foreach ($offers as $offerId => $offer) {
            $offersWithoutDuplicateShop[$offer['shop']['id']] = $offers[$offerId];
        }
        return $pageName == 'homePage' ? array_slice($offersWithoutDuplicateShop, 0, 10) : $offersWithoutDuplicateShop;
    }
}
