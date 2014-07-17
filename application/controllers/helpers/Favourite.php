<?php
class Zend_Controller_Action_Helper_Favourite extends Zend_Controller_Action_Helper_Abstract
{
    public static function getOffers($favoriteShopsOffers)
    {
        $topOffers = array();
        if (count($favoriteShopsOffers)  < 4) {
            $howManyOffersGetFromTopOffers = (5 - count($favoriteShopsOffers));
            $topOffers =
                FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                    "all_popularvaouchercode_list",
                    array(
                        'function' => 'Offer::getTopOffers',
                        'parameters' => array($howManyOffersGetFromTopOffers)
                    )
                );
        }
        $mergedTopOffersAndFavouriteShopsOffers = array_merge($topOffers, $favoriteShopsOffers);
        $offers = '';
        foreach ($mergedTopOffersAndFavouriteShopsOffers as $mergedTopOffersAndFavouriteShopsOffer) {
                $offers[$mergedTopOffersAndFavouriteShopsOffer['id']] = $mergedTopOffersAndFavouriteShopsOffer;
        }
        return $offers;
    }
}
