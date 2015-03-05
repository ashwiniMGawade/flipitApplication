<?php
namespace KC\Repository;
class ShopReasons Extends \KC\Entity\ShopReasons
{
    public static function saveReasons($reasons, $shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->delete('KC\Entity\ShopReasons', 's')
            ->where('s.shop ='.$shopId)
            ->getQuery();
        $query->execute();
        foreach ($reasons as $key => $reason) {
            $entityManagerLocale  = \Zend_Registry::get('emLocale');
            $ShopReasons  = new \KC\Entity\ShopReasons();
            if ($key != '') {
                $ShopReasons->fieldname = $key;
                $ShopReasons->fieldvalue =  $reason;
                $ShopReasons->shop =  \Zend_Registry::get('emLocale')->find('KC\Entity\Shop', $shopId);
                $ShopReasons->deleted =  0;
                $entityManagerLocale->persist($ShopReasons);
                $entityManagerLocale->flush();
            }
        }
        return true;
    }

    public static function getShopReasons($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('s')
            ->from('KC\Entity\ShopReasons', 's')
            ->where('s.shop = '.$shopId);
        $shopReasons = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shopReasons;
    }

    public static function deleteReasons($firstFieldName, $secondFieldName, $shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->delete('KC\Entity\ShopReasons', 's')
            ->where('s.fieldname ="'.$firstFieldName.'"')
            ->andWhere('s.shop = '.$shopId)
            ->getQuery();
        $query->execute();
        
        $queryBuilderSecond = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilderSecond
            ->delete('KC\Entity\ShopReasons', 's')
            ->where('s.fieldname ="'.$secondFieldName.'"')
            ->andWhere('s.shop = '.$shopId)
            ->getQuery();
        $query->execute();
        
        return true;
    }
}