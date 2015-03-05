<?php
class Zend_Controller_Action_Helper_Index extends Zend_Controller_Action_Helper_Abstract
{
    public static function getSpecialListPagesOffers($specialListPages)
    {
        $specialOfferslist = array();
        foreach ($specialListPages as $specialListPage) {
            foreach ($specialListPage as $page) {
                if (!empty($page['page'])) {
                    $specialOfferslistIndex = $page['page']['permalink'] . ',' . $page['page']['pageTitle'];
                    $specialOfferslist[$specialOfferslistIndex] =
                        self::removeDuplicateCode(\KC\Repository\Offer::getSpecialPageOffers($page['page']));
                }
            }
        }
        return $specialOfferslist;
    }

    public static function removeDuplicateCode($offers, $pageName = '')
    {
        $offersWithoutDuplicateShop = '';
        foreach ($offers as $offerId => $offer) {
            $offersWithoutDuplicateShop[$offer['shopOffers']['id']] = $offers[$offerId];
        }
        return $pageName == 'homePage' ? array_slice($offersWithoutDuplicateShop, 0, 10) : $offersWithoutDuplicateShop;
    }
}
