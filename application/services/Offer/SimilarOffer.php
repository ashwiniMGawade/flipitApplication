<?php
class Application_Service_Offer_SimilarOffer extends Application_Service_Offer_OfferListing
{
    public static function fetchSimilarShopOffers($shopId, $shopList, $shopAffiliateprogram)
    {
        $getSimilarShopOffers = self::getSimilarShopOffers($shopId, $shopList);
        if (!empty($getSimilarShopOffers)) {
            if ($shopAffiliateprogram != 0) {
                $getSimilarShopOffers = self::getSlicedNumberOfShopSimilarOffers($getSimilarShopOffers, 3);
            } else {
                $getSimilarShopOffers = self::getSlicedNumberOfShopSimilarOffers($getSimilarShopOffers, 10);
            }
        } else {
            $getSimilarShopOffers = '';
        }
        return $getSimilarShopOffers;
    }

    protected static function getSimilarShopOffers($shopId, $shopList)
    {
        $similarShopsAndSimilarCategoriesOffersKey = 'shop_similarShopsAndSimilarCategoriesOffers'.$shopList;
        $similarShopsAndSimilarCategoriesOffers = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            (string)$similarShopsAndSimilarCategoriesOffersKey,
            array(
               'function' => 'Application_Service_Offer_SimilarOffer::getMergedSimilarShopOffers',
                'parameters' => array($shopId, 30)

            ),
            ''
        );
        return $similarShopsAndSimilarCategoriesOffers;
    }

    public static function getMergedSimilarShopOffers($shopId, $limit)
    {
        $date = date("Y-m-d H:i");
        $similarOffersFromShops = self::getOffersBySimilarShops($date, $limit, $shopId);
        $similarOffersFromShopCategories = self::getOffersBySimilarCategories($date, $limit, $shopId);
        $similarShopsAndSimilarCategoriesOffers = self::mergeSimilarShopsOffersAndSimilarCategoriesOffers(
            $similarOffersFromShops,
            $similarOffersFromShopCategories,
            $limit
        );
        return $similarShopsAndSimilarCategoriesOffers;
    }

    protected static function mergeSimilarShopsOffersAndSimilarCategoriesOffers(
        $similarOffersFromShops,
        $similarOffersFromShopCategories,
        $limit
    )
    {
        return \KC\Repository\Offer::mergeSimilarShopsOffersAndSimilarCategoriesOffers(
            $similarOffersFromShops,
            $similarOffersFromShopCategories,
            $limit
        );
    }

    protected static function getOffersBySimilarShops($date, $limit, $shopId)
    {
        return \KC\Repository\Offer::getOffersBySimilarShops($date, $limit, $shopId);
    }

    protected static function getOffersBySimilarCategories($date, $limit, $shopId)
    {
        return \KC\Repository\Offer::getOffersBySimilarCategories($date, $limit, $shopId);
    }

    protected static function getSlicedNumberOfShopSimilarOffers($offers, $limit)
    {
        $uniqueOffers = self::removeDuplicateShopsOffers($offers);
        $slicedOffers = array_slice($uniqueOffers, 0, $limit);
        return $slicedOffers;
    }
    
    protected static function removeDuplicateShopsOffers($similarShopsOffers)
    {
        $removeDuplicateShop = '';
        foreach ($similarShopsOffers as $offerIndex => $offer) {
            $removeDuplicateShop[$offer['shopOffers']['id']] = $similarShopsOffers[$offerIndex];
        }
        $offersUnique = '';
        foreach ($removeDuplicateShop as $shopIndex => $offer) {
            $offersUnique[] = $removeDuplicateShop[$shopIndex];
        }
        return $offersUnique;
    }
}
new Application_Service_Offer_SimilarOffer();
