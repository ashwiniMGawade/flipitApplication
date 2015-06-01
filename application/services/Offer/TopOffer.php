<?php
class Application_Service_Offer_TopOffer extends Application_Service_Offer_OfferListing
{
    public static function getTopOffers($limit)
    {
        $topCouponCodes = KC\Repository\Offer::getTopCouponCodes(array(), $limit);
    }

}
new Application_Service_Offer_TopOffer();
