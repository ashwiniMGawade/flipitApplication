<?php
/**
 * RouteRedirect
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##kraj## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
class RouteRedirect extends BaseRouteRedirect
{
    /**
     * Retrieve orignalurl and exact redirectto on the basis of REQUEST_URI
     * @author kraj
     * @version 1.0
     */

    public static function getRoute($orignalurl)
    {
        $orignalurl= trim($orignalurl, '/');
        $data = Doctrine_Query::create()
            ->select('r.redirectto')
            ->from('RouteRedirect r')
            ->where("r.orignalurl = ?", FrontEnd_Helper_viewHelper::sanitize($orignalurl))
            ->fetchArray();
        return $data;
    }
    public static function getRedirects($redirectto)
    {
        $q = Doctrine_Query::create()
        ->select('rp.orignalurl')->from('RouteRedirect rp')
        ->where('rp.redirectto="'.$redirectto.'"')->andWhere('deleted = 0')->fetchArray();
        return $q;


    }
    /**
     * add new redirect
     * @param posted form data
     * @author kraj
     * @version 1.0
     */
    public static function addRedirect($params)
    {
        $data = new RouteRedirect();
        $data->orignalurl = BackEnd_Helper_viewHelper::stripSlashesFromString($params['orignalurl']);
        $data->redirectto = BackEnd_Helper_viewHelper::stripSlashesFromString($params['redirectto']);
        $data->save();
    }
    /**
     * getRedirectList fetch all record from database table shop
     * @param $params
     * @return array
     * @author kraj
     * @version 1.0
     */
    public static function getRedirect($params)
    {
        $redirectList = Doctrine_Query::create ()
        ->select ('e.orignalurl as orignalurl,e.redirectto as redirectto,e.created_at as created_at')
        ->from ( "RouteRedirect as e" )
        ->orderBy("e.created_at DESC");
        $list = DataTable_Helper::generateDataTableResponse($redirectList,
                $params,array("__identifier" => 'e.id','e.id','orignalurl','redirectto','created_at'),
                array(),array());

        return $list;
    }
    /**
     * @author kraj
     * @param $id
     * @return ediredirectDetail
     */
    public static function getRedirectForEdit($id)
    {
        $getdata = Doctrine_Query::create()
            ->select("k.*")
            ->from("RouteRedirect as k")
            ->where("k.id =".$id)
            ->fetchArray();
        return $getdata;
    }

    /**
     * @author kraj
     * @param $id
     * @return updated keyword details
     *
     */
    public static function updateRedirect($params)
    {
        $data = Doctrine_Core::getTable('RouteRedirect')
        ->find($params['id']);
        $data->orignalurl = BackEnd_Helper_viewHelper::stripSlashesFromString($params['orignalurl']);
        $data->redirectto = BackEnd_Helper_viewHelper::stripSlashesFromString($params['redirectto']);
        $data->save();
    }

    /**
     * get list of excluded keywords for export
     * @author kraj
     * @return array $redirectList
     * @version 1.0
     */
    public static function exportRedirectList()
    {
        $redirectList = Doctrine_Query::create()
        ->select('e.*')
        ->from("RouteRedirect e")
        ->orderBy("e.id DESC")
        ->fetchArray();
        return $redirectList;

    }

    /**
     * deleted record parmanetly from database.
     * @param $id
     * @author kraj
     * @version 1.0
     */
    public static function deleteRedirect($id)
    {
        $searchbarDel = Doctrine_Core::getTable('RouteRedirect')->find($id);
        $q = Doctrine_Query::create()->delete()
        ->from('RouteRedirect e')
        ->where("e.id=" . $id)
        ->execute();
    }
    
    public function uploadExcel($file, $import = false)
    {
        $uploadResponse = BackEnd_Helper_viewHelper::uploadExcel($file, $import = false);
        return $uploadResponse;
    }
}