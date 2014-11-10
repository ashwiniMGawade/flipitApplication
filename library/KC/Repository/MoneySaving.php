<?php
namespace KC\Repository;
class MoneySaving Extends \KC\Entity\MoneySaving
{
    public static function delartCategories($pageid)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->delete('KC\Entity\MoneySaving', 'ms')
            ->setParameter(1, $pageId)
            ->where('ms.page = ?1')
            ->getQuery();
            $query->execute();
          return true;
    }
}