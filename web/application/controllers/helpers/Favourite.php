<?php
class Zend_Controller_Action_Helper_Favourite extends Zend_Controller_Action_Helper_Abstract
{
    public static function getOffers($favoriteShopsOffers)
    {
        $topOffers = array();
        if (count($favoriteShopsOffers)  < 40) {
            $limitOfTopOffers = (40 - count($favoriteShopsOffers));
            $topOffers = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                $limitOfTopOffers."_popularOffers_list",
                array('function' => '\KC\Repository\Offer::getTopOffers', 'parameters' => array($limitOfTopOffers)),
                ''
            );
        }
        $mergedTopOffersAndFavouriteShopsOffers = array_merge($favoriteShopsOffers, $topOffers);
        unset($mergedTopOffersAndFavouriteShopsOffers['activeCount']);
        return self::removeDuplicateOffers($mergedTopOffersAndFavouriteShopsOffers);
    }

    public static function removeDuplicateOffers($mergedTopOffersAndFavouriteShopsOffers)
    {
        $offers = '';
        foreach ($mergedTopOffersAndFavouriteShopsOffers as $mergedTopOffersAndFavouriteShopsOffer) {
            if (!isset($offers[$mergedTopOffersAndFavouriteShopsOffer['shopOffers']['id']])) {
                $offers[$mergedTopOffersAndFavouriteShopsOffer['shopOffers']['id']] = $mergedTopOffersAndFavouriteShopsOffer;
            }
        }
        return $offers;
    }

    public static function changeStoresPositions($stores)
    {
        $changeStoresPositions = '';
        foreach ($stores as $store) {
            $changeStoresPositions[$store['id']] =  array(
                'id' => $store['id'],
                'imgpath'=>$store['imgpath'],
                'imgname'=>$store['imgname'],
                'name'=>$store['name'],
                'permaLink'=>$store['permaLink'],
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
                'function' => '\KC\Repository\Shop::getPopularStoresForMemeberPortal',
                'parameters' => array(25)
            )
        );
        $stores = self::changeStoresPositions($popularStores);
        return $stores;
    }

    public static function getFavoritesStores()
    {
        $favouriteShops = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache(
                'all_'.Auth_VisitorAdapter::getIdentity()->id.'_favouriteShops',
                array(
                    'function' => '\KC\Repository\Visitor::getFavoriteShops',
                    'parameters' => array(Auth_VisitorAdapter::getIdentity()->id)
                )
            );
        return self::changePostionOfFavortieStores($favouriteShops);
    }

    public static function changePostionOfFavortieStores($favouriteShops)
    {
        $changeStoresPositions = '';
        foreach ($favouriteShops as $store) {
            $changeStoresPositions[$store['id']] =  array(
                'id' => $store['id'],
                'imgpath'=>$store['imgpath'],
                'imgname'=>$store['imgname'],
                'name'=>$store['name'],
                'permaLink'=>$store['permaLink'],
                'activeCount'=>$store['activeCount']
            );
        }
        return $changeStoresPositions;
    }

    

    public static function filterAlreadyFavouriteShops($popularShops, $favouriteShops)
    {
        $removeAlreayAddedFavouriteShops = array();
        foreach ($popularShops as $popularShop) {
            if (!self::inarrayr($popularShop['id'], $favouriteShops)) {
                $removeAlreayAddedFavouriteShops[] = $popularShop;
            }
        }
        return $removeAlreayAddedFavouriteShops;
    }

    public static function inarrayr($needle, $haystack, $strict = false)
    {
        if (!empty($haystack)) {
            foreach ($haystack as $item) {
                if (($strict ? $item === $needle : $item == $needle)
                    || (is_array($item)
                    && self::inarrayr($needle, $item, $strict))
                ) {
                    return true;
                }
            }
        }
        return false;
    }
}
