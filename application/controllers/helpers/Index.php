<?php
class Zend_Controller_Action_Helper_Index extends Zend_Controller_Action_Helper_Abstract
{
    public static function getTopCategoriesIds($topCategories)
    {
        $categoriesIds = '';
        foreach ($topCategories as $topCategory) {
            $categoriesIds[] = $topCategory['categoryId'];
        }

        return $categoriesIds;
    }

    public static function getCategoriesOffers($topCategoriesOffers)
    {
        $topCategoriesOffersWithCategoriesPermalinkIndex = '';
        foreach ($topCategoriesOffers as $topCategoriesOffer) {
            $categoryIndex = $topCategoriesOffer['categoryPermalink'] . "," .$topCategoriesOffer['categoryName'];
            $topCategoriesOffersWithCategoriesPermalinkIndex[$categoryIndex][$topCategoriesOffer['shopId']] =
            $topCategoriesOffer['Offer'];
        }

        return $topCategoriesOffersWithCategoriesPermalinkIndex;
    }

    public static function getSpecialListPagesOffers($specialListPages)
    {
        $specialOfferslist = '';
        foreach ($specialListPages as $specialListPage) {
            foreach ($specialListPage['page'] as $page) {
                $specialOfferslistIndex = $page['permaLink'] . ',' . $page['pageTitle'];
                $specialOfferslist[$specialOfferslistIndex] = Offer::getSpecialPageOffers($page);
            }
        }

        return $specialOfferslist;
    }
}
