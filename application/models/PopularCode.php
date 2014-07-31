<?php

/**
 * PopularCode
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */

class PopularCode extends BasePopularCode
{
    #################################################
    ###### REFACTED CODE ############################
    #################################################
    /**
     * generate papular code by formula
     * @version 1.0
     */
    public static function generatePopularCode($flagForCache)
    {
        $format = 'Y-m-j H:i:s';
        $date = date($format);
        $past4Days = date($format, strtotime('-4 day' . $date));
        $nowDate = $date;

        $newPopularCodes = self::newPopularCode($nowDate, $past4Days, $date);
        self::deleteExpiredPopularCode($date, $flagForCache);

        $allExistingPopularCodes =  self::getAllExistingPopularCode();

        $manullyAddedCodes = self::getOldManuallyAddedPopularCode($allExistingPopularCodes);

        $lengthOfNewPopularCode = count($newPopularCodes);
        $lengthOfOldMainPopularCode = count($manullyAddedCodes);
        $totalPopupLength = $lengthOfNewPopularCode + $lengthOfOldMainPopularCode;

        $newArray = self::mergeNewAndOldPopularCode($totalPopupLength, $manullyAddedCodes, $newPopularCodes);
        self::changePositionPopularCode($newArray, $flagForCache);

        self::getAllPopularCodeByOrder();
        if ($flagForCache==true) {
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularcode_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
        }
        return true;
    }
    public static function newPopularCode($nowDate, $past4Days, $date)
    {
        $newPopularCodes = Doctrine_Query::create()
        ->select('v.id, v.offerId, ((sum(v.onclick)) / (DATEDIFF(NOW(),o.startdate))) as pop, o.startdate')
        ->from('ViewCount v')
        ->leftJoin('v.offer o')
        ->leftJoin('o.shop s')
        ->where('v.updated_at <=' . "'$nowDate' AND v.updated_at >=". "'$past4Days'")
        ->andWhere('o.deleted = 0')
        ->andWhere('s.deleted = 0')
        ->andWhere('s.status = 1')
        ->andWhere('s.affliateProgram = 1')
        ->andWhere('o.offline = 0')
        ->andWhere('o.enddate > "'.$date.'"')
        ->andWhere('o.startdate <= "'.$date.'"')
        ->andWhere("o.discountType ='CD'")
        ->andWhere('o.Visability!="MEM"')
        ->groupBy('v.offerId')->orderBy('pop DESC')->limit(10)
        ->fetchArray();
        //->getSqlQuery();
        //echo $newPopularCodes; die;

        return $newPopularCodes;
    }
    public static function deleteExpiredPopularCode($date, $flagForCache)
    {
        $popIds = Doctrine_Query::create()
        ->select('p.offerId, p.position')
        ->from('PopularCode p')
        ->fetchArray();
        foreach($popIds as $popId):
            $popIdsToDelete = Doctrine_Query::create()
            ->select('o.id')
            ->from('Offer o')
            ->where('o.id ='.$popId['offerId'])
            ->andWhere('o.enddate < "'.$date.'"')
            ->fetchOne();
            if($popIdsToDelete):
                self::deletePopular($popId['offerId'], $popId['position'], $flagForCache);
            endif;
        endforeach;

        return true;
    }

    public static function getAllExistingPopularCode()
    {
        $allExistingPopularCodes = Doctrine_Query::create()
        ->select('p.id,o.id,p.type,p.position')->from('PopularCode p')
        ->leftJoin('p.offer o')->orderBy('p.position')->fetchArray();

        return $allExistingPopularCodes;
    }

    public static function getOldManuallyAddedPopularCode($allExistingPopularCodes)
    {
        $manullyAddedCodes = array();
        foreach ($allExistingPopularCodes as $popular) {
            if ($popular['type'] == 'MN') {
                $manullyAddedCodes[] = $popular;
            }
        }

        return $manullyAddedCodes;
    }

    public static function mergeNewAndOldPopularCode($totalLength, $manullyAddedCodes, $newPopularCodes)
    {
        //echo $totalLength; die;
        $length = 0;
        $lenOldMN = 0;
        $lenNewPop = 0;
        $newArray = array();
        $position = 1;
        //generate new array ( combine with existing and new popular code
        while ($length < $totalLength) {
            if (!empty($manullyAddedCodes[$lenOldMN])) {
                //	die("Raman");
                if ($manullyAddedCodes[$lenOldMN]['position'] == $position) {

                    $Ar = array('type' => $manullyAddedCodes[$lenOldMN]['type'],
                            'offerId' => $manullyAddedCodes[$lenOldMN]['offer']['id'],
                            'position' => $manullyAddedCodes[$lenOldMN]['position']);

                    $newArray[$manullyAddedCodes[$lenOldMN]['offer']['id']] = $Ar;
                    $lenOldMN++;
                    $position++;

                } elseif (!array_key_exists(@$newPopularCodes[$lenNewPop]['offerId'], $newArray)) {

                    $Ar = array('type' => 'AT', 'offerId' => @$newPopularCodes[$lenNewPop]['offerId'],
                            'position' => $position);
                    @$newArray[$newPopularCodes[$lenNewPop]['offerId']] = $Ar;
                    $lenNewPop++;
                    $position++;

                } else {

                    $lenNewPop++;
                }

            } elseif (!array_key_exists($newPopularCodes[$lenNewPop]['offerId'], $newArray)) {

                $Ar = array('type' => 'AT', 'offerId' => $newPopularCodes[$lenNewPop]['offerId'],
                        'position' => $position);
                $newArray[$newPopularCodes[$lenNewPop]['offerId']] = $Ar;
                $lenNewPop++;
                $position++;

            } else {

                $lenNewPop++;
            }

            $length++;
        }

        return $newArray;
    }

    public static function changePositionPopularCode($newArray, $flagForCache)
    {
        $Q = Doctrine_Query::create()->delete()->from('PopularCode s')->where("s.type='AT'")->execute();
        foreach ($newArray as $p) {

            if ($p['type']!='MN') {

                //save popular code in database if new
                $pc = new PopularCode();
                $pc->type = $p['type'];
                $pc->offerId = $p['offerId'];
                $pc->position = $p['position'];
                $pc->save();

                $offerID = $p['offerId'];
                $authorId = self::getAuthorId($offerID);

                $uid = $authorId[0]['authorId'];
                $popularcodekey ="all_". "popularcode".$uid ."_list";

                if ($flagForCache==true) {
                    $flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($popularcodekey);
                    if ($flag) {

                    } else {
                        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($popularcodekey);
                    }
                }

            }
        }

        return true;
    }

    public static function getAllPopularCodeByOrder()
    {
        $offers = Doctrine_Query::create()->select('p.*')
        ->from('PopularCode p')
        ->orderBy('p.position ASC')
        ->fetchArray();
        self::resetPositionOfAllCodes($offers);

        return true;
    }

    public static function resetPositionOfAllCodes($newOfferList)
    {
        $newPos = 1;
        foreach ($newOfferList as $newOffer) {
            $O = Doctrine_Query::create()
            ->update('PopularCode')
            ->set('position', $newPos)
            ->where('id = ?', $newOffer['id']);
            $O->execute();

            $newPos++;
        }

        return true;
    }

    public static function deletePopularCodeAbove27()
    {
        $pc = Doctrine_Query::create()->delete('PopularCode')
        ->where('position > 27')->execute();
    }

    ################################################################
    ########### END REFACTEDRED CODE ###############################
    ################################################################
    /**
     * Search to five offer
     * @param  string  $keyword
     * @param  boolean $flag
     * @version 1.0
     * @return array   $data
     * @author kraj
     */
    public static function searchTopTenOffer($keyword, $flag)
    {
        $format = 'Y-m-j H:i:s';
        $date = date($format);
        $data = Doctrine_Query::create()
        ->select('o.title as title')
        ->from("Offer o")
        ->leftJoin('o.shop s')
        ->where('o.deleted=0')
        ->andWhere('s.deleted = 0')
        ->andWhere('s.status = 1')
        ->andWhere('o.offline = 0')
        ->andWhere('o.enddate > "'.$date.'"')
        ->andWhere('o.startdate <= "'.$date.'"')
        ->andWhere("o.title LIKE ?", "$keyword%")
        ->andWhere('o.discounttype="CD"')
        ->andWhere('o.Visability!="MEM"')
        ->andWhere('o.userGenerated=0')
        //->orderBy("o.title")
        ->limit(10)->fetchArray();

        /*
        $data = Doctrine_Query::create()->select('o.title as title')
                ->from("Offer o")->where('o.deleted=' . "'$flag'")
                ->andWhere("o.title LIKE ?", "$keyword%")
                ->orderBy("o.title ASC")->limit(10)->fetchArray();*/

        return $data;
    }
    public static function searchAllOffer($listOfPopularCode)
    {
        $format = 'Y-m-j H:i:s';
        $date = date($format);
        $data = Doctrine_Query::create()
        ->select('o.title as title,o.id as id')
        ->from("Offer o")
        ->leftJoin('o.shop s')
        ->where('o.deleted=0')
        ->andWhere('s.deleted = 0')
        ->andWhere('o.offline = 0')
        ->andWhere('o.enddate > "'.$date.'"')
        ->andWhere('o.startdate <= "'.$date.'"')
        ->andWhere('o.discounttype="CD"')
        ->andWhere('o.Visability!="MEM"')
        ->andWhere('o.userGenerated=0')
        ->andWhereNotIn('o.id', $listOfPopularCode)
        ->fetchArray();

        return $data;
    }

    /**
     * get popular code list from database
     *
     * @param  integer $limit for total no. of popular codes
     * @author kraj
     * @version 1.0
     * @return array   $data
     */
    public static function getPopularCode($limit = 27)
    {
        $date = date('Y-m-d H:i:s');
        $data = Doctrine_Query::create()
                ->select('p.id,o.title,p.type,p.position,p.offerId')
                ->from('PopularCode p')
                ->leftJoin('p.offer o')
                ->leftJoin('o.shop s')
                ->where('o.deleted =0')
                ->andWhere("o.userGenerated = 0")
                ->andWhere('s.deleted=0')
                ->andWhere('o.offline = 0')
                ->andWhere('o.enddate > "'.$date.'"')
                ->andWhere('o.startdate <= "'.$date.'"')
                ->limit($limit)
                ->orderBy('p.position ASC')
                ->fetchArray();

        $key = 'all_widget5_list';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'all_widget6_list';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

        return $data;
        //echo  "<pre>";
        //print_r($data);
        //die();

    }

    /**
     * get popular offer for voucher code list from database for front end
     * @author Raman
     * @version 1.0
     * @return array $data
     */
    public static function gethomePopularvoucherCode($flag)
    {
        $date = date('Y-m-d H:i:s');
        $data = Doctrine_Query::create()
        ->select('p.id,o.title,o.shopid,o.couponCodeType,o.extendedOffer,o.startDate,o.endDate,o.extendedUrl,o.offerlogoid,o.couponcode,o.exclusivecode,o.discount,o.discountvalueType,s.name,s.permaLink,s.logoid,l.path,l.name,p.type, p.position, p.offerid')
        ->from('PopularCode p')
        ->leftJoin('p.offer o')
        ->leftJoin('o.shop s')
        ->leftJoin('s.logo l')
        ->where('o.deleted =0')
        ->andWhere("(couponCodeType = 'UN' AND (SELECT count(id)  FROM CouponCode WHERE offerid = o.id and status=1)  > 0) or couponCodeType = 'GN'")
        ->andWhere('s.deleted=0')
        ->andWhere('o.offline = 0')
        ->andWhere('s.status = 1')
        ->andWhere('o.enddate > "'.$date.'"')
          ->andWhere('o.startdate <= "'.$date.'"')
        ->andWhere('o.discounttype = "CD"')
        ->andWhere('o.userGenerated = 0')
        ->andWhere('o.Visability != "MEM"')
        ->orderBy('p.position ASC')
        ->limit($flag)
        ->fetchArray();

        return $data;
    }

    /**
     * get popular offer for voucher code list from database for Marktplaat feeds
     *
     * @param integer $limit for number of offers
     * @param array   $ids   id of shops whose we don't want to display on auction site (default shop is Bol.com)
     *
     *
     * @author sp singh
     * @version 1.0
     * @return array $data
     */
    public static function gethomePopularvoucherCodeForMarktplaatFeeds($flag, $id = array(6))
    {
        $date = date('Y-m-d H:i:s');

        $data = Doctrine_Query::create()
            ->select('p.id,o.title,o.shopid,o.couponCodeType,terms.content as terms,o.extendedOffer,o.endDate,o.extendedUrl,o.offerlogoid,o.couponcode,o.exclusivecode,o.discount,o.discountvalueType,s.name,s.permaLink,s.logoid,l.path,l.name,p.type, p.position, p.offerid')
            ->from('PopularCode p')
            ->leftJoin('p.offer o')
            ->leftJoin('o.shop s')
            ->leftJoin('s.logo l')
            ->leftJoin('o.termandcondition terms')
            ->where('o.deleted =0')
            ->andWhere("(couponCodeType = 'UN' AND (SELECT count(id)  FROM CouponCode WHERE offerid = o.id and status=1)  > 0) or couponCodeType = 'GN'")
            ->andWhere('s.deleted=0')
            ->andWhereNotIn('s.id', $id )
            ->andWhere('o.offline = 0')
            ->andWhere('s.status = 1')
            ->andWhere('o.enddate > "'.$date.'"')
            ->andWhere('o.startdate <= "'.$date.'"')
            ->andWhere('o.discounttype = "CD"')
            ->andWhere('o.userGenerated = 0')
            ->andWhere('o.Visability != "MEM"')
            ->orderBy('p.position ASC')
            ->limit($flag)
            ->fetchArray();

        return $data;
    }



    /**
     * add offer in popular code
     * @author kraj
     * @version 1.0
     * @return integer $flag
     */
    public static function addOfferInList($id)
    {
        //find offer by title
        //$title=addslashes($title);
        $Offer = Doctrine_query::create()->from('Offer')
                ->where('id=' . $id)->limit(1)
                ->fetchArray();
        $flag = '0';

        if (sizeof($Offer) > 0) {

            //check offer exist or not
            $pc = Doctrine_Core::getTable('PopularCode')
                    ->findBy('offerId', $id);

            if (sizeof($pc) > 0) {

                $flag = '2';

            } else {

                $flag = '1';
                //find last postion  from database
                $data = Doctrine_Query::create()->select('p.position')
                        ->from('PopularCode p')->orderBy('p.position DESC')
                        ->limit(1)->fetchArray();

                if (sizeof($data) > 0) {

                    $NewPos = $data[0]['position'];

                } else {

                    $NewPos =  0 ;
                }				//add new offer if not exist in datbase
                $pc = new PopularCode();
                $pc->type = 'MN';
                $pc->offerId = $id;
                $pc->position = (intval($NewPos) + 1);
                $pc->save();
                $flag  = array('id'=>$pc->id,'type'=>'MN','offerId'=>$id,'position'=>(intval($NewPos) + 1),'title'=>$Offer[0]['title']);
                //$flag = $pc->toArray();
            }

        }
        //call cache function
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularcode_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodes_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('top20_offers_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('top20_pageHeader_image');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list_shoppage');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');

        return $flag;

    }
    /**
     * delete popular code
     * @param integer $id
     * @param integer $position
     * @author kraj
     * @version 1.0
     */
    public static function deletePapularCode($id, $position)
    {
        if ($id) {
            //delete popular code from list
            $pc = Doctrine_Query::create()->delete('PopularCode')
                    ->where('id=' . $id)->execute();
            //change position by 1 of each below element
            $q = Doctrine_Query::create()->update('PopularCode p')
                    ->set('p.position', 'p.position -1')
                    ->where('p.position >' . $position)->execute();

            // If any position is missing it fixes so that all positions must be there

            $newOfferList = Doctrine_Query::create()->select('p.*')
                ->from('PopularCode p')
                ->orderBy('p.position ASC')
                ->fetchArray();

            $newPos = 1;

            foreach ($newOfferList as $newOffer) {

                $O = Doctrine_Query::create()
                    ->update('PopularCode')
                    ->set('position', $newPos)
                    ->where('id = ?' , $newOffer['id']);

                $O->execute();
                $newPos++;
            }
            //call cache function
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularcode_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodes_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('top20_offers_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('top20_pageHeader_image');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list_shoppage');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
            $key = 'all_widget5_list';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'all_widget6_list';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            return true ;
        }

        return false ;
    }


    /**
     * delete popular code on offer Id basis
     * @param integer $id
     * @param integer $position
     * @author kraj
     * @version 1.0
     */
    public static function deletePopular($id, $position, $flagForCache)
    {
        if ($id) {
            //delete popular code from list
            $pc = Doctrine_Query::create()->delete('PopularCode')
            ->where('offerId=' . $id)->execute();


            //change position by 1 of each below element
            $q = Doctrine_Query::create()->update('PopularCode p')
            ->set('p.position', 'p.position -1')
            ->where('p.position >' . $position)
            ->execute();

            if ($flagForCache==true) {
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularcode_list');
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodes_list');
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('top20_offers_list');
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('top20_pageHeader_image');
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list_shoppage');
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
                $key = 'all_widget5_list';
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
                $key = 'all_widget6_list';
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            }
        }
    }


    /**
     * move up popular code from list
     * @param integer $id
     * @param integer $position
     * @author kraj
     * @version 1.0
     */
    public static function moveUp($id, $position)
    {
        $pos = (intval($position) - 1);
        //find prev element from database based of current
        $PrevPc = Doctrine_Core::getTable('PopularCode')
                ->findBy('position', $pos)->toArray();
        //change position of prev element with current
        //$flag =  1;
        if (count($PrevPc) > 0) {

        //$flag =2;
        $changePrevPc = Doctrine_Core::getTable('PopularCode')
                ->find($PrevPc[0]['id']);
        $changePrevPc->position = $position;
        $changePrevPc->save();
        //change position of current element with postition + 1
        //$pc = Doctrine_Core::getTable('PopularCode')->find($id);
        //$pc->position = $pos;
        //$pc->save();

        $O = Doctrine_Query::create()->update('PopularCode')->set('position', $pos)
             ->where('id = ?' , $id);
        $O->execute();

            //call cache function
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularcode_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('top20_offers_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('top20_pageHeader_image');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list_shoppage');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
            $key = 'all_widget5_list';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'all_widget6_list';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            return true ;
            //return $flag;
        }

        return false ;
    }
    /**
     * move down popular code from list
     * @param integer $id
     * @param integer $position
     * @author kraj
     * @version 1.0
     */
    public static function moveDown($id, $position)
    {
        $pos = (intval($position) + 1);
        //find next element from database based of current
        $PrevPc = Doctrine_Core::getTable('PopularCode')
                ->findBy('position', $pos)->toArray();
        //change position of next element with current
        if (count($PrevPc) > 0) {

            $changePrevPc = Doctrine_Core::getTable('PopularCode')
                    ->find($PrevPc[0]['id']);
            $changePrevPc->position = $position;
            $changePrevPc->save();
            //change position of current element with postition - 1
            $pc = Doctrine_Core::getTable('PopularCode')->find($id);
            $pc->position = $pos;
            $pc->save();
                //call cache function
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularcode_list');
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodes_list');
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('top20_offers_list');
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('top20_pageHeader_image');
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list_shoppage');
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
                $key = 'all_widget5_list';
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
                $key = 'all_widget6_list';
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

                return true ;
            }

            return false ;
    }

    /**
     * Get user Id from offer id
     * @author Raman
     * @return array $userId
     * @version 1.0
     */
    public static function getAuthorId($offerId)
    {
        $userId = Doctrine_Query::create()
        ->select('o.authorId')
        ->from("Offer o")
        ->where("o.id =$offerId")
        ->fetchArray();

        return $userId;
    }


    /**
     * lock element in popular code from list
     * @param integer $id
     * @param integer $position
     * @author Raman
     * @version 1.0
     */
    public static function lockElement($id)
    {
        $lockStatus = Doctrine_Core::getTable('PopularCode')
        ->findBy('offerId', $id)->toArray();
        if (count($lockStatus) > 0) {

            if ($lockStatus[0]['type'] == 'AT') {
                $type = "MN";
            } else {
                $type = "AT";
            }

            $lock = Doctrine_Core::getTable('PopularCode')
            ->find($lockStatus[0]['id']);
            $lock->type = $type;
            $lock->save();

            return true ;
        }

        return false ;

    }

}
