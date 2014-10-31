<?php
namespace KC\Repository;
class Offer Extends \KC\Entity\Offer
{public static function saveNewsticker($params)
     {
            $savenews = new OfferNews();
            $savenews->shopId = @$params['selctedshop'];
            $savenews->title = @BackEnd_Helper_viewHelper::stripSlashesFromString($params['newsTitle']);
            $savenews->url = @BackEnd_Helper_viewHelper::stripSlashesFromString($params['newsrefUrl']);
            $savenews->content = @BackEnd_Helper_viewHelper::stripSlashesFromString($params['newsDescription']);
            $savenews->linkstatus = @BackEnd_Helper_viewHelper::stripSlashesFromString($params['newsdeepLinkStatus']);
            $savenews->startdate = date('Y-m-d',strtotime($params['newsStartDate']));
            $savenews->save();

            //call cache
            $key = 'shop_latestUpdates'  . intval($savenews->shopId) . '_list';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            return $savenews->id ;
    }

  /**
   * get newsticker to show in list
   * @param array $params
   * @return array $newstickerList
   * @author blal fdf
   * @version 1.0
   */
  public static function getnewstickerList($params)
  {
     $newstickerList = Doctrine_Query::create()
                        ->select('n.id as id,n.shopId,n.title as title,n.startdate as startdate,s.name,n.linkstatus')
                        ->from("OfferNews n")
                        ->leftJoin("n.shop s")
                        ->where("n.deleted = 0")
                        ->orderBy("n.title ASC");

        $list = DataTable_Helper::generateDataTableResponse($newstickerList,
                $params, array("__identifier" => 'n.id', 'n.id','n.title','s.name','n.startdate','n.linkstatus'),
                array(), array());

        return $list;

    }
    /**
     * deleted newsticker by id
     * @param integer $params
     * @author blal
     * @version 1.0
     */
    public static function deletenewsticker($id)
    {
        $del1 = Doctrine_Query::create()
               ->delete()
               ->from('OfferNews n')
               ->where("n.id=" . $id)
               ->execute();

        //call cache
        $key = 'shop_latestUpdates'  . intval($savenews->shopId) . '_list';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

    }

    /**
     * details of editable newsticker
     * @param integer $id
     * @return array $data
     * @author blal
     * @version 1.0
     */
    public static function getNewsticker($id)
    {
        $data = Doctrine_Query::create()->select("n.*,s.name")
                ->from('OfferNews n')
                ->LeftJoin("n.shop s")
                ->where("id = ?", $id)
                ->addWhere('deleted=0')
                ->fetchArray();
        return $data;
    }

    /**
     * update newsticker
     * @param integer $id
     * @return array $data
     * @author blal
     * @version 1.0
     */
    public static function updateNewsticker($params)
    {
            $data = Doctrine_Core::getTable('OfferNews')->find( $params['id'] );
            $data->shopId = $params['selctedshop'];
            $data->offerId = null;
            $data->title = BackEnd_Helper_viewHelper::stripSlashesFromString($params['newsTitle']);
            $data->url = BackEnd_Helper_viewHelper::stripSlashesFromString($params['newsrefUrl']);
            $data->content = BackEnd_Helper_viewHelper::stripSlashesFromString($params['newsDescription']);
            $data->linkstatus =  $params['newsdeepLinkStatus'];
            $data->startdate = date('Y-m-d',strtotime($params['newsStartDate']));
            $data->save();

            //call cache
            $key = 'shop_latestUpdates'  . intval($data->shopId) . '_list';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            return $savenews->id ;
    }


    /**
     * getAllUrls
     *
     * returns the realted store page url
     * @param integer $id  offer_news id
     * @author Surinderpal Singh
     * @return array array of urls
     */
    public static function getAllUrls($id)
    {
        $data  = Doctrine_Query::create()->select("s.permaLink,on.id")
                ->from('OfferNews on')
                ->leftJoin("on.shop s")
                ->where("on.id=? " , $id)
                ->fetchOne(null, Doctrine::HYDRATE_ARRAY);

        $urlsArray = array();

        # check an offer news has related shop or not
        if(isset($data['shop']) && $data['shop']['permaLink']) {
            $urlsArray[] = $data['shop']['permaLink'] ;
        }
        return $urlsArray ;
    }