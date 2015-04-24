<?php
class Zend_Controller_Action_Helper_Index extends Zend_Controller_Action_Helper_Abstract
{
    public static function getSpecialListPagesOffers($specialListPages)
    {
        $specialOfferslist = '';
        foreach ($specialListPages as $specialListPage) {
            $specialOfferslistIndex = $specialListPage['page']['permalink'] . ',' . $specialListPage['page']['pageTitle'];
            $specialOfferslist[$specialOfferslistIndex] =
            \KC\Repository\SpecialPagesOffers::getSpecialPageOfferById($specialListPage['page']['id'], 10);

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

    public static function getSpecialPageIdsAfterTraverse($specialPageIds)
    {
        $specialPageIdArray = array();
        foreach ($specialPageIds as $specialPageId) {
            $specialPageIdArray[] = $specialPageId['specialpageId'];
        }
        return $specialPageIdArray;
    }

    public static function categoriesOffers($categoryIds)
    {
        $categoriesOffers = array();
        foreach ($categoryIds as $categoryId) {
            $categoriesOffers[$categoryId['category']['permaLink']] =
            \KC\Repository\Category::getCategoryVoucherCodes($categoryId['category']['id'], 10, 'homePage');
        }
        return $categoriesOffers ;
    }
}
