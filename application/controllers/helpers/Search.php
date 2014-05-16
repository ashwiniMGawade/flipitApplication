<?php
class Zend_Controller_Action_Helper_Search extends Zend_Controller_Action_Helper_Abstract
{
    public function getExcludedShopIdsBySearchedKeywords($searchedKeywords)
    {
        $excludedKeywords = ExcludedKeyword::getExcludedKeywords($searchedKeywords);
        $shopIds = '';

        if (!empty($excludedKeywords[0])) :
            if($excludedKeywords[0]['action'] == 0):
                header('location: '.$excludedKeywords[0]['url']);
                exit();
            else:
                $shopIds = self::getShopIdsByExcludedKeywords($excludedKeywords[0]);
            endif;
        endif;

        return $shopIds;
    }

    public static function getShopIdsByExcludedKeywords($excludedKeywords)
    {
        $shopIds = array();
        foreach ($excludedKeywords['shops'] as $shops) :
            $shopIds[] = $shops['shopsofKeyword'][0]['id'];
        endforeach;
        return $shopIds;
    }

    public static function getshopsByExcludedShopIds($shopIds)
    {
        $shopsForSearchPage = array();
        $shopsByShopIds = Shop::getShopsByShopIds($shopIds);

        foreach ($shopsByShopIds as $shopsByShopId) :
            $shopsForSearchPage[$shopsByShopId['id']] = $shopsByShopId;
        endforeach;

        return $shopsForSearchPage;
    }

    public static function getPopularStores($searchedKeywords)
    {
        $popularStores = Shop::getStoresForSearchByKeyword($searchedKeywords, 8);
        $popularStoresForSearchPage = self::getPopularStoresForSearchPage($popularStores);
        return $popularStoresForSearchPage;
    }

    public static function getPopularStoresForSearchPage($popularStores)
    {
        $popularStoresForSearchPage = array();

        foreach ($popularStores as $popularStore) :
            $popularStoresForSearchPage[$popularStore['id']] = $popularStore;
        endforeach;

        return $popularStoresForSearchPage;
    }

    public static function getStoresForSearchResults($shopsByShopIds, $popularShops)
    {        
        if (!empty($shopsByShopIds) && !empty($popularShops)) :
            $shopsForSearchPage = array_merge($shopsByShopIds, $popularShops);
        else:
            $shopsForSearchPage = !empty($popularShops) ? $popularShops : $shopsByShopIds;
        endif;

        return $shopsForSearchPage;
    }
}