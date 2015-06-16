<?php
class Zend_Controller_Action_Helper_Index extends Zend_Controller_Action_Helper_Abstract
{
    public static function getSpecialListPagesOffers($specialListPages)
    {
        $specialOfferslist = array();
        foreach ($specialListPages as $specialListPage) {
            $specialOfferslistIndex = $specialListPage['page']['permalink'] . ',' . $specialListPage['page']['pageTitle'];
            $specialOfferslist[$specialOfferslistIndex] = self::removeDuplicateCode(
                \KC\Repository\Offer::getSpecialPageOffersByFallBack(
                    $specialListPage['page'],
                    'home'
                ),
                'specialPage'
            );

        }
        return $specialOfferslist;
    }

    public static function removeDuplicateCode($offers, $pageName = '')
    {
        $offersWithoutDuplicateShop = '';
        foreach ($offers as $offerId => $offer) {
            if ($pageName == 'specialPage') {
                $offerData = $offer['offers']['shopOffers'];
            } else {
                $offerData = $offer['shopOffers'];
            }

            $offersWithoutDuplicateShop[$offerData['id']] = $offers[$offerId];
        }
        return array_slice($offersWithoutDuplicateShop, 0, 10);
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
            $categoryOffers =
            \KC\Repository\CategoriesOffers::getCategoryOffersByCategoryIdForFrontEnd($categoryId['category']['id']);
            if (empty($categoryOffers)) {
                $categoryOffers = self::removeDuplicateCode(
                    \KC\Repository\Category::getCategoryVoucherCodes($categoryId['category']['id'], 50, 'homePage'),
                    'homePage'
                );
            }
            $categoriesOffers[$categoryId['category']['permaLink']] = $categoryOffers;
        }
        return $categoriesOffers;
    }
}
