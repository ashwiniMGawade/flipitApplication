<?php
namespace KC\Repository;

class Conversions extends \KC\Entity\Conversions
{
    public static function getConversionId($id, $ip, $type = 'offer')
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select("c.id, c.subid")
            ->from("KC\Entity\Conversions", "c")
            ->where("c.IP = ". $ip);
        if ($type == 'offer') {
            $query->andWhere("c.offer= ". $id);
        } else {
            $query->andWhere("c.shop= ". $id);
        }
        return $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public static function updateConverted($subId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $queryBuilder
            ->update('KC\Entity\Conversions', 'c')
            ->set('c.converted', 1)
            ->where('c.subid = '. $subId)
            ->getQuery()
            ->execute();
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_conversion_details');
        return true;
    }

    public static function getConversionDetail($subId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        return  $queryBuilder->select('c.subid,c.utma,c.utmz,o.id,s.id,n')
            ->from("KC\Entity\Conversions", "c")
            ->leftJoin("c.offer", "o")
            ->leftJoin("o.shop", "s")
            ->leftJoin("s.affliatenetwork", "n")
            ->where('subid = '. $subId)
            ->getQuery()
            ->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public static function getConversionInformationById($id)
    {
        $conversionInfo = array();
        
        if (is_numeric($id)) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $conversionInfo = $queryBuilder->select('c.id,o.title as offerTitle,s.name as shopName,cat.name as categoryName')
                ->from("KC\Entity\Conversions", "c")
                ->leftJoin("c.offer", "o")
                ->leftJoin("o.shop", "s")
                ->leftJoin("o.category", "cat")
                ->where("c.id = ". $id)
                ->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }
        return $conversionInfo;
    }
}
