<?php
class ShopReasons extends BaseShopReasons
{
    public static function saveReasons($reasons, $shopId)
    {
        Doctrine_Query::create()
            ->delete('shopreasons s')
            ->where('s.shopid ='.$shopId)
            ->execute();
        foreach ($reasons as $key => $reason) {
            $ShopReasons  = new ShopReasons();
            if ($reason != '') {
                $ShopReasons->fieldname = $key;
                $ShopReasons->fieldvalue =  $reason;
                $ShopReasons->shopid =  $shopId;
                $ShopReasons->deleted =  0;
                $ShopReasons->save();
            }
        }
        return true;
    }

    public static function getShopReasons($shopId)
    {
        $shopReasons = Doctrine_Query::create()
            ->select('*')
            ->from('shopreasons')
            ->where('shopid = '.$shopId)
            ->fetchArray();
        return $shopReasons;
    }

    public static function deleteReasons($firstFieldName, $secondFieldName, $thirdFieldName, $forthFieldName, $shopId)
    {
        Doctrine_Query::create()
        ->delete('shopreasons s')
        ->where('s.fieldname ="'.$firstFieldName.'"')
        ->andWhere('s.shopid = '.$shopId)
        ->execute();
        
        Doctrine_Query::create()
        ->delete('shopreasons s')
        ->where('s.fieldname ="'.$secondFieldName.'"')
        ->andWhere('s.shopid = '.$shopId)
        ->execute();

        Doctrine_Query::create()
        ->delete('shopreasons s')
        ->where('s.fieldname ="'.$thirdFieldName.'"')
        ->andWhere('s.shopid = '.$shopId)
        ->execute();

        Doctrine_Query::create()
        ->delete('shopreasons s')
        ->where('s.fieldname ="'.$forthFieldName.'"')
        ->andWhere('s.shopid = '.$shopId)
        ->execute();

        return true;
    }
}
