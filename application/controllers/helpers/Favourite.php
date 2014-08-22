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

    public static function changeStoresPositions($stores)
    {
        $changesStorePosition = '';
        foreach ($stores as $store) {
            $changesStorePosition[] =  array(
                'id' => $store['shop']['id'],
                'imgpath'=>$store['imgpath'],
                'imgname'=>$store['imgname'],
                'name'=>$store['shop']['name'],
                'permaLink'=>$store['shop']['permaLink'],
                 'activeCount'=>$store['activeCount']
            );
        }
        return $changesStorePosition;
    }

    public static function getPopularStores()
    {
        $topStores = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            "25_popularshop_list",
            array(
                'function' => 'Shop::getPopularStores',
                'parameters' => array(25)
            )
        );
        $stores = self::changeStoresPositions($topStores);
        return $stores;
    }
}
