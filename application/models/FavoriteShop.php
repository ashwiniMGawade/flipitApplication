<?php

/**
 * FavoriteShop
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
class FavoriteShop extends BaseFavoriteShop
{
    ####################### refactored code ################
    public static function filterAlreadyFavouriteShops($popularShops)
    {
        $visitorFavouriteShops = Doctrine_Query::create()->select()
            ->from('FavoriteShop')
            ->where('visitorId = '. Auth_VisitorAdapter::getIdentity()->id)
            ->fetchArray();
        $favouriteShops = array();
        foreach ($visitorFavouriteShops as $visitorFavouriteShop) {
            $favouriteShops[] = $visitorFavouriteShop['shopId'];
        }
        $removeAlreayAddedFavouriteShops = array();
        foreach ($popularShops as $popularShop) {
            if (!in_array($popularShop['shopId'], $favouriteShops)) {
                $removeAlreayAddedFavouriteShops[] = $popularShop;
            }
        }
        return $removeAlreayAddedFavouriteShops;
    }
    ###################### END REFACTORED CODE #############

    public static function get_suggestionshops($userid,$flag)
    {
        $lastdata=FavoriteShop::get_allshops($userid);
        if(sizeof($lastdata)>0){
            for($i=0;$i<sizeof($lastdata);$i++){
                $shopdata[$i]=$lastdata[$i]['shopId'];
            }
            $shopvalues=implode(",", $shopdata);
            $data = Doctrine_Query::create()->select('s.name as name,s.id as id,fav.store_id')
            ->from("Signupfavoriteshop fav")->leftJoin('fav.shop s')->where('s.deleted= ?' , $flag )->andWhere("s.status= ?",1)
            ->andWhere("fav.store_id=s.id")->andWhere("fav.store_id NOT IN ($shopvalues)")->orderBy("s.name ASC")
            ->limit(10)->fetchArray();
        } else {
            $data = Doctrine_Query::create()->select('s.name as name,s.id as id,fav.store_id ')
            ->from("Signupfavoriteshop fav")->leftJoin('fav.shop s')->where('s.deleted= ?' , $flag )->andWhere("s.status= ?",1)
            ->andWhere("fav.store_id=s.id")->orderBy("s.name ASC")
            ->limit(10)->fetchArray();
        }
        return $data;
    }
    public static function delete_favshop($id)
    {
        if($id){
            //delete particular code from list
            $pc = Doctrine_Query::create()->delete('FavoriteShop')
            ->where('id=' . $id)->execute();
        }
    }
    public static function get_allshops($userid)
    {
        $data = Doctrine_Query::create()
        ->select('p.id,p.visitorId,p.shopId,p.created_at,s.name,s.id,l.path,l.name as image,v.id')
        ->from("FavoriteShop p")
        ->leftJoin('p.visitors v')
        ->leftJoin('p.shops s')
        ->leftJoin('s.logo l')
        ->andWhere("p.shopId=s.id")->andWhere("s.status= ?",1)->andWhere("s.deleted= ?",0)->andWhere("p.visitorId=$userid")->orderBy("s.name ASC")->fetchArray();
        return $data;
    }
    public static function searchallToptenFavshops($keyword,$flag,$userid)
    {
        $data = Doctrine_Query::create()->select('s.name as name,s.id as id')
        ->from("Shop s")->where('s.deleted= ?' , $flag )
        ->andWhere("s.status= ?",1)
        ->andWhere("s.name LIKE ?", "$keyword%")->orderBy("s.name ASC")
        ->limit(10)->fetchArray();
        return $data;
    }
    public static function searchToptenFavshops($keyword,$flag,$userid)
    {
        $suggestiondata=FavoriteShop::get_suggestionshops($userid,0);
        if(sizeof($suggestiondata)>0){
            for($i=0;$i<sizeof($suggestiondata);$i++){
                $shopsuggestiondata[$i]=$suggestiondata[$i]['store_id'];
            }
            $shopsuggestionvalues=$shopsuggestiondata;
        } else{
            $shopsuggestionvalues="";
        }
        $lastdata=FavoriteShop::get_allshops($userid);
        if(sizeof($lastdata)>0){
            for($i=0;$i<sizeof($lastdata);$i++){
                $shopdata[$i]=$lastdata[$i]['shopId'];
            }
            if($shopsuggestionvalues!=''){
                $shopdata=array_merge($shopdata,$shopsuggestionvalues);
            }
            $shopvalues=implode(",", $shopdata);
            $data = Doctrine_Query::create()->select('s.name as name,s.id as id')
        ->from("Shop s")->where('s.deleted= ?' , $flag )->andWhere("s.status= ?",1)
        ->andWhere("s.name LIKE ?", "$keyword%")->andWhere("s.id NOT IN ($shopvalues)")->orderBy("s.name ASC")
        ->limit(10)->fetchArray();
        } else{
            if($shopsuggestionvalues!="") {
                $shopsuggestionvalues=implode(",", $shopsuggestionvalues);
            } else{
                $shopsuggestionvalues=0;
            }
            $data = Doctrine_Query::create()->select('s.name as name,s.id as id')
            ->from("Shop s")->where('s.deleted= ?' , $flag )->andWhere("s.status= ?",1)
            ->andWhere("s.name LIKE ?", "$keyword%")->andWhere("s.id NOT IN ($shopsuggestionvalues)")->orderBy("s.name ASC")
            //->andWhere("s.name LIKE ?", "$keyword%")->orderBy("s.name ASC")
            ->limit(10)->fetchArray();
        }

        return $data;
    }

    public static function addshop($userid,$shopid)
    {
        $pc = new FavoriteShop();
        $pc->visitorId = $userid;
        $pc->shopId = $shopid;
        $pc->save();

        $shop = Doctrine_Core::getTable('Shop')->find($shopid);
        $key = 'all_shopdetail'  . $shopid . '_list';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'all_relatedShopInStore'  . $shopid  . '_list';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newoffer_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('top_20_offers_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list_feed');

        return $shop->toArray();

        //call cache function
        //FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_signupfavoriteshop_list');

    }
    public static function voucher_percentage($id)
    {
        $data = Doctrine_Query::create()->select("o.*")->
        from("Offer o")
        ->where('o.authorId='."'$id'")
        ->fetchArray();
        $percentage=0;
        $calc_records=count($data);
        if($calc_records>0){
            $percentage=5;
        }
        return $percentage;
    }
    public static function calculate_percentage($id)
    {
        $data = Doctrine_Query::create()->select("v.*")->
        from("FavoriteShop v")
        ->where('v.visitorId='."'$id'")
        ->fetchArray();
        $percentage=0;
        $calc_records=count($data);
        if($calc_records==0){
            $percentage=0;
        } else if($calc_records==1){
            $percentage=5;
        } else if($calc_records==2){
            $percentage=10;
        } else if($calc_records==3){
            $percentage=15;
        } else if($calc_records==4){
            $percentage=20;
        } else if($calc_records>=5){
            $percentage=30;
        }
        return $percentage;
    }

    /**
     * This will delete favorite shops which are selected by the logged in user
     * @author cbhopal
     * @param $shopid,$userid integer
     * @return boolean
     * @version 1.0
     */
    public static function delFavoriteShops($shopid,$userid)
    {
        $shop = Doctrine_Core::getTable('Shop')->find($shopid);

        $getSelectedShops = Doctrine_Query::create()->delete('FavoriteShop')
                                                    ->where('visitorId = '. $userid)
                                                    ->andWhere('shopId = '. $shopid)
                                                    ->execute();


        $key = 'all_shopdetail'  . $shopid . '_list';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'all_relatedShopInStore'  . $shopid  . '_list';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newoffer_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('top_20_offers_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list_feed');

        return $shop->toArray();
    }
}
