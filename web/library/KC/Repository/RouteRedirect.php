<?php
namespace KC\Repository;

class RouteRedirect extends \Core\Domain\Entity\RouteRedirect
{
    public static function getRoute($orignalurl)
    {
        $orignalurl= trim($orignalurl, '/');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('route')
            ->from('\Core\Domain\Entity\RouteRedirect', 'route')
            ->setParameter(1, \FrontEnd_Helper_viewHelper::sanitize($orignalurl))
            ->where('route.orignalurl = ?1');
        $routeRedirectInfo = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $routeRedirectInfo;
    }

    public static function getRedirects($redirectto)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('route.orignalurl')
            ->from('\Core\Domain\Entity\RouteRedirect', 'route')
            ->setParameter(1, $redirectto)
            ->where('route.redirectto = ?1')
            ->setParameter(2, '0')
            ->andWhere('route.deleted = ?2');
        $orignalUrl = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $orignalUrl;
    }

    public static function addRedirect($params)
    {
        $routeRedirect = new \Core\Domain\Entity\RouteRedirect();
        $routeRedirect->orignalurl = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['orignalurl']);
        $routeRedirect->redirectto = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['redirectto']);
        $routeRedirect->deleted = 0;
        $routeRedirect->created_at = new \DateTime('now');
        $routeRedirect->updated_at = new \DateTime('now');
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $entityManagerLocale->persist($routeRedirect);
        $entityManagerLocale->flush();
        self::refreshVarnishUrl($routeRedirect->orignalurl);
        return true;
    }

    public static function getRedirect($params)
    {
        $request  = \DataTable_Helper::createSearchRequest($params, array('id','orignalurl', 'redirectto', 'created_at'));
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder()->from('\Core\Domain\Entity\RouteRedirect', 'r');
        if (isset($params['id']) && $params['id']) {
            $queryBuilder->where('r.id = '.$params['id']);
        }
        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder
            ->setQueryBuilder($queryBuilder)
            ->add('number', 'r.id')
            ->add('text', 'r.orignalurl')
            ->add('text', 'r.redirectto')
            ->add('number', 'r.created_at');

        $results = $builder->getTable()->getResponseArray();

        return $results;
    }

    public static function getRedirectForEdit($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('route')
            ->from('\Core\Domain\Entity\RouteRedirect', 'route')
            ->setParameter(1, $id)
            ->where('route.id = ?1');
        $routeRedirectInfo = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $routeRedirectInfo;
    }

    public static function updateRedirect($params)
    {
        $entityManagerLocale  =\Zend_Registry::get('emLocale');
        $routeRedirect =  $entityManagerLocale->find('\Core\Domain\Entity\RouteRedirect', $params['id']);
        $routeRedirect->orignalurl = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['orignalurl']);
        $routeRedirect->redirectto = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['redirectto']);
        $routeRedirect->updated_at = new \DateTime('now');
        $entityManagerLocale->persist($routeRedirect);
        $entityManagerLocale->flush();
        self::refreshVarnishUrl($routeRedirect->orignalurl);
        return true;
    }

    public static function exportRedirectList()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('route')
            ->from('\Core\Domain\Entity\RouteRedirect', 'route')
            ->orderBy('route.id', 'DESC');
        $routeRedirectList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $routeRedirectList;
    }

    public static function deleteRedirect($id)
    {
        $entityManagerLocale  =\Zend_Registry::get('emLocale');
        $routeRedirect =  $entityManagerLocale->find('\Core\Domain\Entity\RouteRedirect', $id);
        $entityManagerLocale->remove($routeRedirect);
        $entityManagerLocale->flush();
        self::refreshVarnishUrl($routeRedirect->orignalurl);
        return true;
    }

    public static function uploadExcel($file, $import = false)
    {
        $uploadResponse = \BackEnd_Helper_viewHelper::uploadExcel($file, $import, $type = '');
        return $uploadResponse;
    }

    public static function searchRedirects($keyword)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('r.orignalurl, r.id')
            ->from('\Core\Domain\Entity\RouteRedirect', 'r')
            ->where('r.deleted=0')
            ->andWhere("r.orignalurl LIKE '%$keyword%'")
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function refreshVarnishUrl($redirectUrl)
    {
        $varnishObj = new \KC\Repository\Varnish();
        $varnishObj->addUrl($redirectUrl);
    }
}
