<?php
class Application_Service_Offer_SimilarOffer extends Application_Service_Offer_OfferListing
{
    public static function festchSimilarShopOffers($shopId, $shopList, $shopAffiliateprogram)
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
    
}
new Application_Service_Offer_SimilarOffer();
