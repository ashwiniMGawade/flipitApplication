<?php
namespace KC\Repository;

class ViewCount extends \KC\Entity\ViewCount
{

    public function __contruct($connectionName = "")
    {
        if (!$connectionName) {
            $connectionName = "doctrine_site" ;
        }
        Doctrine_Manager::getInstance()->bindComponent($connectionName, $connectionName);
    }

    public static function processViewCount($id = null)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
                ->update('KC\Entity\ViewCount', 'v')
                ->set('v.counted', 1)
                ->where('v.counted = 0');
        # set counted against given id
        if ($id) {
            $query = $query->andWhere('v.viewcount ='.$id);
        }
        $query->getQuery()->execute();
    }

}
