<?php
namespace KC\Repository;

class OfferTiles extends \KC\Entity\OfferTiles
{

    public function __contruct($connectionName = "")
    {
        if (!$connectionName) {
            $connectionName = "doctrine_site" ;
        }
        Doctrine_Manager::getInstance()->bindComponent($connectionName, $connectionName);
    }

    public static function getAllTiles()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('t.id, t.label, t.type, t.ext, t.path, t.name, t.position')
        ->from('KC\Entity\OfferTiles', 't');
        $allTile = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $Ar = array();
        foreach ($allTile as $t) {

            $Ar[$t['type']][] = $t;
        }
        return $Ar;
    }
}
