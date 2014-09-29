<?php
namespace KC\Repository;

class RoutePermalink extends \KC\Entity\RoutePermalink
{
    public static function getRoute($permalink)
    {
        $permalink = trim($permalink, '/');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('p')
            ->from('KC\Entity\RoutePermalink', 'p')
            ->setParameter(1, $permalink)
            ->where('p.permalink = ?1');
        $routeRedirectInfo = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $routeRedirectInfo;
    }

    public static function getPageProperties($permalink)
    {
        $permalink = trim($permalink, '/');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('p')
            ->from('KC\Entity\Page', 'p')
            ->setParameter(1, $permalink)
            ->where('p.permalink = ?1')
            ->orderBy('p.id', 'DESC')
            ->setMaxResults(1);
         $pageDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return  $pageDetails;
    }

    public static function getPermalinks($exactLink)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('rp.permalink')
            ->from('KC\Entity\RoutePermalink', 'rp')
            ->setParameter(1, $exactLink)
            ->where('rp.exactlink = ?1')
            ->setParameter(2, '0')
            ->andWhere('rp.deleted = ?2');
        $pageDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return  $pageDetails;
    }

    public static function getDefaultPageProperties($slug)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('p')
            ->from('KC\Entity\Page', 'p')
            ->setParameter(1, $slug)
            ->where('p.slug = ?1');
        $pageDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return  $pageDetails;
    }
}
