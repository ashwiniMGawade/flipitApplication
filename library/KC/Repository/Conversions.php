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
        Doctrine_Query::create()
        ->update('Conversions')
        ->set('converted', 1)
        ->where('subid = ?', $subId)->execute();
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_conversion_details');
        return true;
    }

    public static function getConversionDetail($subId)
    {
        return  Doctrine_Query::create()->select('c.subid,c.utma,c.utmz,o.id,s.id,n.*')
            ->from("Conversions c")
            ->leftJoin("c.offer o")
            ->leftJoin("o.shop s")
            ->leftJoin("s.affliatenetwork n")
            ->where('subid = ?', $subId)
            ->fetchOne(null, Doctrine::HYDRATE_ARRAY);
    }
}
