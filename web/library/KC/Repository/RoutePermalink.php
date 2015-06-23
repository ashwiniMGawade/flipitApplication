<?php
namespace KC\Repository;

class RoutePermalink extends \Core\Domain\Entity\RoutePermalink
{
    public static function getRoute($permalink)
    {
        $permalink = trim($permalink, '/');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('r.permalink, r.exactlink')
            ->from('KC\Entity\RoutePermalink', 'r')
            ->setParameter(1, \FrontEnd_Helper_viewHelper::sanitize($permalink))
            ->where('r.permalink = ?1');
        $routeRedirectInfo = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $routeRedirectInfo;
    }

    public static function getPageProperties($permalink)
    {
        $permalink = trim($permalink, '/');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('p.id')
            ->from('KC\Entity\Page', 'p')
            ->setParameter(1, \FrontEnd_Helper_viewHelper::sanitize($permalink))
            ->where('p.permalink = ?1');
         $pageDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return  $pageDetails;
    }

    public static function getPermalinks($exactLink)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('rp.permalink')
            ->from('KC\Entity\RoutePermalink', 'rp')
            ->setParameter(1, \FrontEnd_Helper_viewHelper::sanitize($exactLink))
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

    public static function updateRoutePermalink($permalink, $exactlink, $validatedPermalink)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->update('KC\Entity\RoutePermalink', 'p')
            ->set('p.permalink', $queryBuilder->expr()->literal($permalink))
            ->set('p.type', $queryBuilder->expr()->literal('SHP'))
            ->set('p.exactlink', $queryBuilder->expr()->literal($exactlink))
            ->where('p.type = '.$queryBuilder->expr()->literal('SHP'))
            ->andWhere("p.permalink = ".$queryBuilder->expr()->literal($validatedPermalink))
            ->getQuery()
            ->execute();
        return true;
    }

    public static function saveRoutePermalink($permalink, $exactlink)
    {
        $routePermalink = new \KC\Entity\RoutePermalink();
        $routePermalink->permalink = $permalink;
        $routePermalink->type = 'SHP';
        $routePermalink->exactlink = $exactlink;
        $routePermalink->created_at = new \DateTime('now');
        $routePermalink->updated_at = new \DateTime('now');
        $routePermalink->deleted = 0;
        $entityManagerLocale  = \Zend_Registry::get('emLocale');
        $entityManagerLocale->persist($routePermalink);
        $entityManagerLocale->flush();
    }

    public static function validatePermalink($permalink)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('p.permalink')
            ->from('KC\Entity\RoutePermalink', 'p')
            ->where("p.permalink =".$queryBuilder->expr()->literal($permalink))
            ->andWhere('p.type ='.$queryBuilder->expr()->literal('SHP'));
        $validatedPermalink = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $validatedPermalink;
    }
}
