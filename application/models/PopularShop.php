<?php

/**
 * PopularShop
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##Er.kundal## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
class PopularShop extends BasePopularShop
{


    /**
     * Search to Ten offer
     * @param string $keyword
     * @param boolean $flag
     * @version 1.0
     * @return array $data
     * @author Er.kundal
     */
    public static function searchTopTenshop($keyword, $flag)
    {
        $lastdata=self :: getPopularShop();

        if(sizeof($lastdata)>0){
            for($i=0;$i<sizeof($lastdata);$i++){
                $shopdata[$i]=$lastdata[$i]['shopId'];
            }

            $shopvalues = implode(",", $shopdata);

        }else{
            $shopvalues = '0';
        }


        $data = Doctrine_Query::create()->select('o.name as name')
        ->from("Shop o")->where('o.deleted=' . "'$flag'")
        ->andWhere("o.status = 1")
        ->andWhere("o.name LIKE ?", "$keyword%")
        ->andWhere("o.id NOT IN ($shopvalues)")
        ->orderBy("o.name ASC")->limit(10)->fetchArray();

        return $data;
    }
    /**
     * generate papular code by formula
     * @author Er.kundal
     * @version 1.0
     */
    public static function generatePopularCode()
    {
        $format = 'Y-m-j 00:00:00';
        $date = date($format);
        // - 4 days from today
        $past4Days = date($format, strtotime('-4 day' . $date));
        $nowDate = $date;
        //echo 'lessFour='.$past4Days;
        //echo 'current='	. $nowDate;
        $NewpapularCode = Doctrine_Query::create()->select('v.id,v.offerId')
        ->from('ViewCount v')
        ->where('v.updated_at <=' . "'$nowDate' AND v.updated_at >="
                . "'$past4Days'")
                //->leftJoin('v.offer o')
        ->groupBy('v.offerId')->orderBy('v.onClick DESC')->limit(10)
                ->fetchArray();
                //echo "New offer";
        //echo "<pre>";
        //print_r($NewpapularCode);
        //die();

        //get last position id from database
        $lastPostionOffer = Doctrine_Query::create()->select('p.position')
                ->from('PopularCode p')->orderBy('p.position DESC')->limit(1)
                        ->fetchArray();

                        if(sizeof($lastPostionOffer) > 0){
                        $lastPos = intval($lastPostionOffer[0]['position']) + 1;
        } else {

        $lastPos = 1;
        }		//echo $lastPos;

        //get all existing popular code from database
        $allExistingOffer = Doctrine_Query::create()
        ->select('p.id,o.id,p.type,p.position')->from('PopularCode p')
        ->leftJoin('p.offer o')->orderBy('p.position')->fetchArray();
        //echo "Popular code";
        //echo "<pre>";
        //print_r($allExistingOffer);
        //die();
        $temp = array();
        //loop for generate temp array for maching purpose
        foreach ($NewpapularCode as $popular) {

        $temp[$popular['offerId']] = $popular;
        }
        //echo "Key Array";
        //echo "<pre>";
        //print_r($temp);
        $newArray = array();
        //generate new array ( combine with existing and new popular code
        foreach ($temp as $key => $t) {

        if(sizeof($allExistingOffer) > 0){

            foreach ($allExistingOffer as $exist) {

                if ($key == $exist['offer']['id']) {

                    $Ar = array('type' => $exist['type'],
                        'offerId' => $exist['offer']['id'],
                        'position' => $exist['position']);
                        $newArray[$key] = $Ar;

                        //echo $exist['position'];

                    } else {

                        if (!array_key_exists($key, $newArray)) {

                        $Ar = array('type' => 'AT', 'offerId' => $key,
                            'position' => $lastPos);
                            $newArray[$key] = $Ar;

                            if (!array_key_exists($exist['offer']['id'], $temp)) {
        $lastPos++;
                        }
                        }
                        }
                        }
                        } else {

                        $Ar = array('type' => 'AT', 'offerId' => $key,
        'position' => $lastPos);
        $newArray[$key] = $Ar;

        $lastPos++;

        }
        }
        //die();
        //echo "LAST NEW ARRAY";
        //echo "<pre>";
        //print_r($newArray);
        //die();
        foreach ($newArray as $p) {

        //find popular code by offer
        $pc = Doctrine_Core::getTable('PopularCode')
        ->findBy('offerId', $p['offerId']);

            if (sizeof($pc) > 0) {
        } else {
            //save popular code in database if new
            $pc = new PopularCode();
            $pc->type = 'AT';
            $pc->offerId = $p['offerId'];
                $pc->position = $p['position'];
                $pc->save();
            }
            }
            //call cache function
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
            //die();
            }

            /**
         * get popular shop list from database
         * @author Er.kundal
         * @version 1.0
         * @return array $data
         */
         public static function getPopularShop()
         {
            $data = Doctrine_Query::create()
            ->select('p.id,s.name,p.type,p.position,p.shopId')
            ->from('PopularShop p')
            ->leftJoin('p.shop s')
            ->orderBy('p.position ASC')
            ->fetchArray();
            return $data;

            }

/**
* add Shop in popular SHOP
* @author Er.kundal
* @version 1.0
* @return integer $flag
*/
public static function addShopInList($title)
{
            //find SHOP by title
                $title = addslashes($title);
            $shop = Doctrine_query::create()->from('Shop')
            ->where('name=' . "'$title'")->andWhere('status=1')->andWhere('deleted=0')->limit(1)->fetchArray();

            $flag = '2';
            if (sizeof($shop) > 0) {

                    //check offer exist or not
                    $pc = Doctrine_Core::getTable('PopularShop')
                        ->findBy('shopId', $shop[0]['id']);

                        if (sizeof($pc) > 0) {
                    } else {

                    $flag = '1';
                    //find last postion  from database
                    $data = Doctrine_Query::create()->select('p.position')
                        ->from('PopularShop p')->orderBy('p.position DESC')
                        ->limit(1)->fetchArray();
                        if(sizeof($data) > 0){

                        $NewPos = $data[0]['position'];

                        } else {

                        $NewPos = 1;
                    }				//add new offer if not exist in datbase
                    $pc = new PopularShop();
                    $pc->type = 'MN';
                    $pc->shopId = $shop[0]['id'];
                    $pc->position = (intval($NewPos) + 1);
                    $pc->save();
                    $flag = $pc->toArray();
                    }

}
//call cache function
FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('25_popularshop_list');
FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
return $flag;

    }
    /**
    * delete popular shop
    * @param integer $id
    * @param integer $position
    * @author Er.kundal
    * @version 1.0
    */
                    public static function deletePapularCode($id, $position)
                    {
                        if ($id) {
                            //delete popular code from list
                            $pc = Doctrine_Query::create()->delete('PopularShop')
                            ->where('id=' . $id)->execute();
                            //change position by 1 of each below element
                           
                            //call cache function
                            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('25_popularshop_list');
                            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
                            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('search_pageHeader_image');

                        }
                    }
                    /**
                     * move up Popular Shop from list
                     * @param integer $id
                     * @param integer $position
                     * @author Er.kundal
                     * @version 1.0
                     */
                    public static function moveUp($id, $position)
                    {
                        $pos = (intval($position) - 1);

                        //find prev element from database based of current
                        $PrevPc = Doctrine_Core::getTable('PopularShop')
                        ->findBy('position', $pos)->toArray();

                        $pid = @$PrevPc[0]['id'];
                        //change position of prev element with current
                        $changePrevPc = Doctrine_Core::getTable('PopularShop')
                        ->find($pid);

                        if($changePrevPc) {
                            $changePrevPc->position = $position;
                            $changePrevPc->save();
                            //change position of current element with postition + 1
                            $pc = Doctrine_Core::getTable('PopularShop')->find($id);
                            $pc->position = $pos;
                            $pc->save();
                            //call cache function
                            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('25_popularshop_list');
                            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
                            return true ;
                        }
                        return false ;
                    }
                    /**
                     * move down Popular Shop from list
                     * @param integer $id
                     * @param integer $position
                     * @author Er.kundal
                     * @version 1.0
                     */
                    public static function moveDown($id, $position)
                    {
                        $pos = (intval($position) + 1);
                        //find next element from database based of current
                        $PrevPc = Doctrine_Core::getTable('PopularShop')
                        ->findBy('position', $pos)->toArray();

                        $pid = @$PrevPc[0]['id'];


                        //change position of next element with current
                        $changePrevPc = Doctrine_Core::getTable('PopularShop')
                        ->find($pid);

                        if($changePrevPc) {
                            $changePrevPc->position = $position;
                            $changePrevPc->save();
                            //change position of current element with postition - 1
                            $pc = Doctrine_Core::getTable('PopularShop')->find($id);
                            $pc->position = $pos;
                            $pc->save();
                            //call cache function
                            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('25_popularshop_list');
                            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
                            return true ;
                        }
                        return false ;



                    }

    public static function savePopularShopsPosition($shopId)
    {
        $databaseConnection = Doctrine_Manager::getInstance()->getConnection('doctrine_site')->getDbh();
        $databaseConnection->query('SET FOREIGN_KEY_CHECKS = 0;');
        $databaseConnection->query('TRUNCATE TABLE popular_shop');
        $databaseConnection->query('SET FOREIGN_KEY_CHECKS = 1;');
        unset($databaseConnection);
        $shopId = explode(',', $shopId);
        $i = 1;
        foreach ($shopId as $shopIdValue) {
            $popularShop = new PopularShop();
            $popularShop->shopId = $shopIdValue;
            $popularShop->position = $i;
            $popularShop->type = "MN";
            $popularShop->save();
            $i++;
        }
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularShops_list');
    }

}
