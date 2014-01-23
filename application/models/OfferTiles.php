<?php

/**
 * Offer
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
class OfferTiles extends BaseOfferTiles
{
	/**
     * function to add offer tiles 
     * @author blal
     */
    public static function addOfferTile($params,$ext=""){

    	if($params['forDelete']){
    		
    		$data = Doctrine_Core::getTable('OfferTiles')->find($params['forDelete']);
    		
    	} else {
    		
    		$data = new OfferTiles();
    	}
    	$data->type = $params['hidtype'];
    	$data->label = $params['label'];
    	$data->name = $params['hidimage'];
    	$data->ext = $ext;
    	$data->path = "images/upload/offertiles/";
    	$data->position = $params['position'];
    	$data->save();
    	$id = $data->id;
		return $id;
    	
    }
    /**
     * 
     * @param unknown_type $TileId
     */
    public static function getOfferTilesList($TileId){
    	$oneTile = '';
    	if($TileId)
    	{
    		 $oneTile = Doctrine_Core::getTable('OfferTiles')->find($TileId)->toArray();
    		
    	}
    	return $oneTile;
    }
    
    public static function deleteMenuRecord($params){
    	$del = Doctrine_Query::create()->delete()
    	->from('OfferTiles ot')
    	->where("ot.id=".@$params['id'])
    	->execute();
    	return true;
    }
    
   public static function getAllTiles() {
    	
    	 $allTile = Doctrine_Core::getTable('OfferTiles')->findAll()->toArray();
    	 $Ar = array();
    	 foreach ($allTile as $t){
    	 	
    	 	$Ar[$t['type']][] = $t;
    	 }
    	 return $Ar;
    	 
    	}
}