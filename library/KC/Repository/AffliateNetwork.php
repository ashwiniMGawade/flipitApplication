<?php
namespace KC\Repository;

class AffliateNetwork extends \KC\Entity\AffliateNetwork
{

   /**
   * save new network
   * @param array $params
   * @author blal
   * @version 1.0
   */
   public function addNewnetwork($params)
   {
       $data = new AffliateNetwork();
       $data->name = BackEnd_Helper_viewHelper::stripSlashesFromString($params['addNetworkText']);
       $data->subId = BackEnd_Helper_viewHelper::stripSlashesFromString($params['subId']);
       $data->status = '1';
       $data->save ();
       //call cache function
       FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_affilatenetwork_page');
       return $data;
  }

    public static function getNetworkList($params = "")
    {
        $srh =  @$params["searchText"] != 'undefined' ? @$params["searchText"] : '';
        $sortBy = isset($params['sortBy']) ? @$params['sortBy'] : 'id DESC';
        $delVal = isset($params['off']) ?  $params['off'] : '0, 1';
        
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $networkList = $queryBuilder->select('a.name as name ,a.id, a.subId')
            ->from("KC\Entity\AffliateNetwork", "a")
            ->where("a.name LIKE $srh%")
            ->andWhere("a.deleted = 0")
            ->where('a.status IN('.$delVal.')')
            ->andWhere("a.affliate_networks IS NULL")
            ->orderBy($sortBy)
            ->getQuery();

        $list =  \DataTable_Helper::generateDataTableResponse(
            $networkList,
            $params,
            array("__identifier" => 'a.id','a.id','a.name','a.subid'),
            array(),
            array()
        );
        return $list;

    }

 /**
  * get top five networks
  * @param string $keyword
  * @return array $data
  * @author blal
  * @version 1.0
  */
  public static function searchTopFiveNetwork($keyword)
  {
    $data = Doctrine_Query::create()->select('a.name as name')
                                    ->from("AffliateNetwork as a")
                                    ->where('a.deleted=0')
                                    ->andWhere('a.status=1')
                                    ->andWhere("a.name LIKE ?", "$keyword%")
                                    ->andWhere("a.replaceWithId IS NULL")
                                    ->orderBy("a.name ASC")
                                    ->limit(5)->fetchArray();
        return $data;
 }

 /**
  *change status of networks
  * @param array $params
  * @author blal
  * @version 1.0
  */
  public static function changeStatus($params)
  {
    $status = $params['status']=='offline' ? '0' : '1';
    $q = Doctrine_Query::create()
                         ->update('AffliateNetwork a')
                         ->set('a.status', $status)
                         ->where('a.id=?',$params['id'])
                         ->execute();

 }

 /**
  * details of editable network
  * @param integer $id
  * @return array $data
  * @author blal
  * @version 1.0
  */
 public static function getNetworkForEdit($id)
 {
    $data = Doctrine_Query::create()->select("a.*")
                                    ->from('AffliateNetwork a')
                                    ->where("id = ?", $id)
                                    ->fetchArray();
    return $data;

 }

 /**
  * update edited network by id
  * @param array $params
  * @author blal updated by kraj
  * @version 1.0
  */
 public static function updateNetwork($params)
 {
    //find network by id
    self::replaceNetwork($params);


    $data = Doctrine_Core::getTable('AffliateNetwork')->find($params['id']);
    $data->name = BackEnd_Helper_viewHelper::stripSlashesFromString($params["addNetworkText"]);

    if(isset($params["subId"])) {
        $data->subId = BackEnd_Helper_viewHelper::stripSlashesFromString($params["subId"]);
    }
    $a = $data->save();
    //call cache function
    FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_affilatenetwork_page');
 }

 /**
  * delete network by id
  * @param integer $params
  * @author blal
  * @version 1.0
  */
 public static function deleteNetwork($params)
 {
    $q = Doctrine_Query::create()->update('AffliateNetwork a')
                                 ->set('a.deleted', 1)
                                 ->where('a.id=?', $params['id'])
                                 ->execute();
    //call cache function
    FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_affilatenetwork_page');
 }

 /**
  * get network list in edit form in dropdown(replace with:)
  * @author blal
  * @version 1.0
  */
 public static function networklistDropdown($params = "")
 {
    $networkList = Doctrine_Query::create ()
                                 ->select ('a.name as name ,a.id, a.status as status ,a.replaceWithId as replaceWithId')
                                 ->from ( "AffliateNetwork as a" )
                                 ->Where("a.deleted = 0" )
                                 ->andWhere('a.id!=?',$params['id'])
                                 ->andWhere("a.replaceWithId IS NULL")
                                 ->orderBy("a.name ASC")
                                ->fetchArray();

    return $networkList;


 }

 /**
  * replace network name with network name in dropdown(Edit form)
  * @author blal updated by kraj
  * @version 1.0
  */
  public function replaceNetwork($params)
  {
    if(intval($params['selectNetworkList'] > 0 )) {

    $q = Doctrine_Query::create()
                         ->update('AffliateNetwork a')
                         ->set('a.replaceWithId', $params['selectNetworkList'])
                         ->where('a.id=?',$params['networkUpdatedId'])
                         ->execute();

    }
 }
}
