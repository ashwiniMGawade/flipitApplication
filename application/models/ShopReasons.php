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
            if ($key != '') {
                $ShopReasons->fieldname = $key;
                $ShopReasons->fieldvalue =  $reason;
                $ShopReasons->shopid =  $shopId;
                $ShopReasons->deleted =  0;
                $ShopReasons->save();
            }
        }
        return true;
    }

    public function getShopReasons($shopId)
    {
        $shopReasons = Doctrine_Query::create()
            ->select('*')
            ->from('shopreasons')
            ->where('shopid = '.$shopId)
            ->fetchArray();
        return $shopReasons;
    }

    public static function deleteReasons($id, $shopId)
    {
        for ($i = $id; $i <= $id +1; $i++) {
            Doctrine_Query::create()
            ->delete('shopreasons s')
            ->where('s.id ='.$i)
            ->andWhere('s.shopid = '.$shopId)
            ->execute();
        }
    }
}