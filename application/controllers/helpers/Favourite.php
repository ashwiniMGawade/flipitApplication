<?php
class Zend_Controller_Action_Helper_Favourite extends Zend_Controller_Action_Helper_Abstract
{
    public static function getOffers($favoriteShopsOffers)
    {
        $topOffers = array();
        if (count($favoriteShopsOffers)  < 40) {
            $limitOfTopOffers = (40 - count($favoriteShopsOffers));
            $topOffers = \KC\Repository\Offer::getTopOffers($limitOfTopOffers);
        }
        $mergedTopOffersAndFavouriteShopsOffers = array_merge($topOffers, $favoriteShopsOffers);
        return self::removeDuplicateOffers($mergedTopOffersAndFavouriteShopsOffers);
    }

    public static function removeDuplicateOffers($mergedTopOffersAndFavouriteShopsOffers)
    {
        $offers = '';
        foreach ($mergedTopOffersAndFavouriteShopsOffers as $mergedTopOffersAndFavouriteShopsOffer) {
            if (!isset($offers[$mergedTopOffersAndFavouriteShopsOffer['shop']['id']])) {
                $offers[$mergedTopOffersAndFavouriteShopsOffer['shop']['id']] = $mergedTopOffersAndFavouriteShopsOffer;
            }
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

    public static function changeStoresPositions($stores)
    {
        $changeStoresPositions = '';
        foreach ($stores as $store) {
            $changeStoresPositions[] =  array(
                'id' => $store['shop']['id'],
                'imgpath'=>$store['imgpath'],
                'imgname'=>$store['imgname'],
                'name'=>$store['shop']['name'],
                'permaLink'=>$store['shop']['permaLink'],
                 'activeCount'=>$store['activeCount']
            );
        }
        return $changeStoresPositions;
    }

    public static function getPopularStores()
    {
        $popularStores = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            "25_popularshop_list",
            array(
                'function' => 'Shop::getPopularStores',
                'parameters' => array(25)
            )
        );
        $stores = self::changeStoresPositions($popularStores);
        return $stores;
    }
}
