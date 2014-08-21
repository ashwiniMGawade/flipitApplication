<?php
class Zend_Controller_Action_Helper_Favourite extends Zend_Controller_Action_Helper_Abstract
{
    public static function getOffers($favoriteShopsOffers)
    {
        $topOffers = array();
        if (count($favoriteShopsOffers)  < 40) {
            $limitOfTopOffers = (40 - count($favoriteShopsOffers));
            $topOffers = Offer::getTopOffers($limitOfTopOffers);
        }
        $mergedTopOffersAndFavouriteShopsOffers = array_merge($topOffers, $favoriteShopsOffers);
        return self::removeDuplicateOffers($mergedTopOffersAndFavouriteShopsOffers);
    }

    public static function removeDuplicateOffers($mergedTopOffersAndFavouriteShopsOffers)
    {
        $offers = '';
        foreach ($mergedTopOffersAndFavouriteShopsOffers as $mergedTopOffersAndFavouriteShopsOffer) {
            $offers[$mergedTopOffersAndFavouriteShopsOffer['id']] = $mergedTopOffersAndFavouriteShopsOffer;
        }

        usort(
            $offers,
            "self::usortFavouriteOffers"
        );
        
        return $offers;
    }

    public static function usortFavouriteOffers($favouriteOffersAsc, $favouriteOffersDesc)
    {
        return isset($favouriteOffersDesc['fvid']) ? $favouriteOffersDesc['fvid'] : '';
    }
}
