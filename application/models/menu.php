<?php

/**
 * menu
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
class menu extends Basemenu
{
	public static function getAllTree() {
			$treeObject     = Doctrine_Core::getTable('mainmenu')->getTree();
			return $treeObject;
	}
/**
 * Insert root menu in the databse 
 * @param $params
 * @author mkaur
 */
 public static function insertOne($params)
  {
  	if ($params['hidimage']!='') {
  		$image = new Image();
  		$image->path = $params['hidimage'];
  		$image->name = BackEnd_Helper_viewHelper::stripSlashesFromString($params['hidimageorg']);
  		$image->type = 'menuIcon';
  		$image->save();
  	}

  	$menu = new menu();
  	$menu->name = BackEnd_Helper_viewHelper::stripSlashesFromString(htmlentities( $params['label'], ENT_QUOTES , 'UTF-8'));
  	$menu->url = BackEnd_Helper_viewHelper::stripSlashesFromString($params['url']);
  	$menu->parentId=0;
  	if($params['position']!=''){
  	$menu->position = $params['position'];
  	}
  	if($params['position']==''){
  		$menu->position = 1;
  	}
  	if($params['hidimage']!=''){
  	$menu->iconId = $image->id;
  	}
  	$menu->save();

  	if($params['url']!=''){
  	$getRecord = Doctrine_Core::getTable('RoutePermalink')
  	->findOneBy('exactlink', $params['url']);
  	//print_r($getRecord);die;
 	if(!empty($getRecord) > 0){
  		$del = Doctrine_Query::create()->delete('RoutePermalink')->where('exactlink = "'. $params['url'].'"')->execute();
  	}
  	
  	$route = new RoutePermalink();
  	$route->permalink = BackEnd_Helper_viewHelper::stripSlashesFromString($params['url']);
  	$route->type = 'PG';
  	$route->exactlink = BackEnd_Helper_viewHelper::stripSlashesFromString($params['url']);
  	$route->save();
  	}
  	
  	$treeObject = Doctrine_Core::getTable('menu')->getTree();
  	$treeObject->createRoot($menu);
  	
  	//print_r($treeObject); die;
  	return $params;
  
  }
  /**
   * insert Parent menu inthe database
   * @param $params
   * @author mkaur
   */
public static function insertNode($params) {
	//$nodeObject = $menu->getNode();
  	//echo $params['hid'];die;
	
	if ($params['hidimage']!='') {
		$image = new Image();
		$image->path = $params['hidimage'];
		$image->name = $params['hidimageorg'];
		$image->type = 'menuIcon';
		$image->save();
	}
	
	$child1 = new menu();
  	$child1->name = BackEnd_Helper_viewHelper::stripSlashesFromString(htmlentities( $params['label'] , ENT_QUOTES , 'UTF-8'));
  	$child1->url = BackEnd_Helper_viewHelper::stripSlashesFromString($params['url']);
  	$child1->position = $params['position'];
  	$child1->parentId = $params['hid'];

  	if ($params['hidimage']!=''){
  	$child1->iconId = $image->id;
  	}
  	
  	$menu = Doctrine_Core::getTable('menu')->findOneById($params['hid']);
  	$child1->getNode()->insertAsLastChildOf($menu);
  
  	//$child2->getNode()->insertAsLastChildOf($menu);
  	if($params['roothid']!=''){
  	return $params['roothid'];
  	}
  	else{
  	return $params['hid'];
  	}
}
	public static function deleteOne(){
  	
  	$menu = Doctrine_Core::getTable('menu')->findOneByName('Child menu 1');
  	$menu->getNode()->delete();
  }
 public static function moveOne()
 {
 	$menu = new menu();
 	$menu->name = 'Root menu 2';
 	$menu->save();
 	
 	$menuTable = Doctrine_Core::getTable('menu');
 	$treeObject    = $menuTable->getTree();
 	$treeObject->createRoot($menu);
 	
 	$childmenu = $menuTable->findOneByName('Child menu 1');
 	$childmenu->getNode()->moveAsLastChildOf($menu);
 }
 public static function getNodeType()
 {
 	$menu = Doctrine_Core::getTable('menu')->findOneByName('Child menu 1');
 	$isLeaf = $menu->getNode()->isLeaf();
 	$isRoot = $menu->getNode()->isRoot();
 }
 public static function getSiblings()
 {
 	$menu = Doctrine_Core::getTable('menu')->findOneByName('Child menu 1');
 	$hasNextSib = $menu->getNode()->hasNextSibling();
 	$hasPrevSib = $menu->getNode()->hasPrevSibling();
 	/*
 	$hasChildren = $menu->getNode()->hasChildren();
	$hasParent   = $menu->getNode()->hasParent();
	$firstChild = $menu->getNode()->getFirstChild();
	$lastChild  = $menu->getNode()->getLastChild();
	
	$children = $menu->getNode()->getChildren();
	
	$descendants = $menu->getNode()->getDescendants();
	$ancestors   = $menu->getNode()->getAncestors();
	
	$numChildren    = $menu->getNode()->getNumberChildren();
	$numDescendants = $menu->getNode()->getNumberDescendants();

 	 */
 }
 public static function getInTreeFormat()
 {
 	$q = Doctrine_Query::create()
    ->select('c.name, p.name, m.name')
    ->from('menu c')
    //->leftJoin('c.HottestProduct p')
    //->leftJoin('p.Manufacturer m')
    ->setHydrationMode(Doctrine_Core::HYDRATE_ARRAY);
 	$treeObject = Doctrine_Core::getTable('menu')->getTree();
 	$treeObject->setBaseQuery($q);
 	$tree       = $treeObject->fetchTree();
 	$treeObject->resetBaseQuery();
 }
 public static function getWithIndention()
 {
 	$array = array();
 	$treeObject     = Doctrine_Core::getTable('menu')->getTree();
 	//print_r($treeObject);die;
 	
 	$rootColumnName = $treeObject->getAttribute('rootColumnName');
 	foreach ($treeObject->fetchRoots() as $root) {
 		$options = array(
 				'root_id' => $root->$rootColumnName
 		);
 		foreach($treeObject->fetchTree($options) as $node) {
 			
 			$array .= str_repeat(' ', $node['level']) . $node['name'];
 		}
 		
 	}
    $data = self::getarray($array);
 	//return $array;
 }
 
 /*public static function getarray($array){
 	foreach ($array as $version => $options['label']){
 		print_r($options);die;
 	}
 	
 }*/
public static function getMainMenuList(){
		$menuList = Doctrine_Query::create()
		->select('m.*')
		->from("menu m")
		->orderBy("m.position")
		->fetchArray();
		return $menuList;
	}
public static function getmenuList(){ 
 $menuList = Doctrine_Query::create()
		->select('m.*')
		->from("menu m")
		->where("level=0")
		->orderBy("m.position asc")
		->fetchArray();
 		//print_r($mediaList);die;
	 return $menuList;
	}

 public static function getmenuRecord($id){
 	$data = Doctrine_Query::create()->select("m.*,i.path,i.name")
 	->from('menu m')
 	->leftJoin("m.menuIcon i")
 	->where("m.id =". $id)
 	->fetcharray(null , Doctrine::HYDRATE_ARRAY);
 	return $data;
	
 }
 public static function editmenuRecord($params){
 	//print_r($params);die;
 	
 	if($params['position']=='' || $params['position']==0){
 	$position = '1';		
 	}else{
 		$position = '"'.$params['position'].'"';
 	}
 	$imageid = null;
 	
 	if($params['imageid']!=''){
 	$imageid = $params['imageid'];
 	$image = Doctrine_Query::create()
 	->update('Image i')
 	->set('i.path','"'.$params['hidimage'].'"')
 	->set('i.name','"'.$params['hidimageorg'].'"')
 	->where('i.id='.$params['imageid']);
 	$image->execute();
 	}
 	
 	if($params['imageid']=='' && $params['hidimage']!=''){
 		//print_r($params);die;
 		$image = new Image();
 		$image->path = '"'.$params['hidimage'].'"';
 		$image->name = '"'.$params['hidimageorg'].'"';
 		$image->type = 'menuIcon';
 		$image->save();
 		$imageid = $image->id;
 	}
 	
 	$data = Doctrine_Query::create()
 	->update('menu m')
 	->set('m.name',"'". BackEnd_Helper_viewHelper::stripSlashesFromString(htmlentities( $params['label'], ENT_QUOTES , 'UTF-8'))."'")
 	->set('m.url',"'". BackEnd_Helper_viewHelper::stripSlashesFromString($params['url']) ."'")
 	->set('m.position',$position)
 	->set('m.iconId','"'.$imageid.'"')
 	->where('m.id='.'"'.$params['hid'].'"');
 	$data->execute();
 	//print_r($params);die;aaaaa
 	return $params;
 }
 
public static function getrtmenuRecord($id){
	$menuList = Doctrine_Query::create()
	->select('m.*')
	->from("menu m")
	->where('m.root_id='.$id)
	->orderBy('m.level ASC')
	->addOrderBy('m.position')
	->fetchArray();
	return $menuList;
} 
/**
 * get minimum position 
 * @return array
 */
public static function gethighposition(){
	$menuList = Doctrine_Query::create()
	->select('m.*,MIN(position)')
	->from("menu m")
	->where('m.level=0')
	->fetchArray();
	//print_r($menuList);die;
return $menuList;
}

/**
 * Delete menu records from database
 * @author mkaur
 */
public static function deleteMenuRecord($params){
	$del = Doctrine_Query::create()->delete()
		->from('menu m')
			->where("m.id=".@$params['id'])
		->execute();
	if($params['parentId']==0 || $params['parentId']==null){
		$del = Doctrine_Query::create()->delete()
			->from('menu m')
			->where("m.root_id=".@$params['id'])
			->execute();
		}
		
	if($params['parentId']!=0 && $params['parentId']!=null){
		$del = Doctrine_Query::create()->delete()
		->from('menu m')
		->where("m.parentId=".@$params['id'])
		->execute();
	}	
		
	return true;
}

/**
 * Delete menu records from database
 * @author Raman
 */
public static function deleteAllMenuRecord(){
	$del = Doctrine_Query::create()->delete()
	->from('menu m')
	->execute();
	return true;
}

//********************FRONT-END FUNCTION********************//
/**
 * get menu from datbase where parent =0
 * @author kraj
 * @version 1.0
 * @return array $all
 */
public static function getLevelFirst() {

	$all = Doctrine_Query::create()->from('menu')->orderBy('position')->where('parentId=0')->fetchArray();
	return $all;
		
}
/**
 * get menu from datbase by parent on second level
 * @author kraj
 * @version 1.0
 * @return array $second
 */
public static function getLevelSecond($id) {
	 
	$second = Doctrine_Query::create()
	->from('menu')
	->orderBy('position')
	->where('parentId='.$id)
	->andWhere('parentId='.$id)
	->fetchArray();
	return $second;
}
/**
 * get menu from datbase by parent on third level
 * @author kraj
 * @version 1.0
 * @return array $th
 */
public static function getLevelThird($id)
{
	$third = Doctrine_Query::create()->from('menu')->orderBy('position')->where('parentId='.$id)->fetchArray();
	return $third;
}
} 