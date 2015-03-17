<?php
/**
 * RoutePermalink
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##cbhopal## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
class RoutePermalink extends BaseRoutePermalink
{
    /**
     * Retrieve permalink and exact link on the basis of REQUEST_URI
     * @author cbhopal
     * @version 1.0
     */
    public static function getRoute($permalink)
    {
        $permalink= trim($permalink, '/');
        $data = Doctrine_Query::create()
            ->select('r.permalink, r.exactlink')
            ->from('RoutePermalink r')
            ->where("r.permalink = ?", FrontEnd_Helper_viewHelper::sanitize($permalink))
            ->fetchArray();
        return $data;
    }
    public static function getPageProperties($permalink)
    {
        $permalink= trim($permalink, '/');
        $pageDetails = Doctrine_Query::create()
        ->select('p.id')
        ->from('Page p')
        ->where("permalink = ?", $permalink)
        ->fetchArray();
        return $pageDetails;
    }
    public static function getPermalinks($exactLink)
    {
        $q = Doctrine_Query::create()
        ->select('rp.permalink')->from('RoutePermalink rp')
        ->where("rp.exactlink='?'" , $exactLink )->andWhere('deleted = 0')->fetchArray();
        return $q;
    }

    public static function getDefaultPageProperties($slug)
    {
        $data = Doctrine_Query::create()->select('p.*')
        ->from('Page p')
        ->where("slug = ?", $slug)
        ->fetchArray();
        return $data;
    }
}
