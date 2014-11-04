<?php
namespace KC\Repository;

class Chain extends \KC\Entity\Chain
{
	######### refactored code #################
    public static function updateChainItemLocale($newLocale, $oldLocale)
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->update('KC\Entity\Website', 'w')
        		->leftJoin('w.chain', 'c')
                ->set("c.locale", $queryBuilder->expr()->literal($newLocale))
                ->setParameter(1, $queryBuilder->expr()->literal($oldLocale))
                ->where('c.locale = ?1')
                ->getQuery();
        $query->execute();
        return true;
    }
    ######### end refactored code #################
}