<?php

/**
 * SpecialList
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##Er.kundal## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
class SpecialList extends BaseSpecialList
{
    ######################################################
    ################# REFACTORED CODE ####################
    ######################################################
    public static function getSpecialPages($limit = 0)
    {
        $currentDateAndTime = date('Y-m-d H:i:s');
        $specialPages = Doctrine_Query::create()
        ->select('sp.type,sp.position,sp.specialpageId,p.*,l.*')
        ->addSelect("(SELECT count(*) FROM refOfferPage roc LEFT JOIN roc.Offer off LEFT JOIN off.shop s  WHERE roc.pageid = sp.specialpageId and off.deleted = 0 and s.deleted = 0 and off.enddate >'".$currentDateAndTime."' and off.startdate <= '".$currentDateAndTime."'  and off.discounttype='CD'  and off.Visability!='MEM') as totalCoupons")
        ->addSelect("(SELECT count(*) FROM refOfferPage roc1 LEFT JOIN roc1.Offer off1 LEFT JOIN off1.shop s1  WHERE roc1.pageid = sp.specialpageId and off1.deleted = 0 and s1.deleted = 0 and off1.enddate >'".$currentDateAndTime."' and off1.startdate <= '".$currentDateAndTime."' and off1.Visability!='MEM') as totalOffers")
        ->from('SpecialList sp')
        ->leftJoin('sp.page p')
        ->leftJoin('p.logo l')
        ->where('p.deleted = 0')
        ->andWhere('p.publish = 1')
        ->limit($limit)
        ->orderBy('sp.position ASC')->fetchArray();
        return $specialPages;
    }

    public static function getSpecialPagesIds()
    {
        $currentDateAndTime = date('Y-m-d H:i:s');
        $specialPageDetails = Doctrine_Query::create()
            ->select('sp.specialpageid, sp.total_offers, sp.total_coupons, p.*,l.*')
            ->from('SpecialList sp')
            ->leftJoin('sp.page p')
            ->leftJoin('p.logo l')
            ->where('p.deleted = 0')
            ->andWhere('p.publish = 1')
            ->orderBy('sp.position ASC')
            ->fetchArray();
        return $specialPageDetails;
    }

    public static function updateTotalOffersAndTotalCoupons($totalOffers, $totalCoupons, $specialPageId)
    {
        if (!empty($specialPageId)) {
            Doctrine_Query::create()
                ->update('SpecialList sl')
                ->set('sl.total_offers', $totalOffers)
                ->set('sl.total_coupons', $totalCoupons)
                ->where('sl.specialpageid ='.$specialPageId)
                ->execute();
        }
        return true;
    }
    ####################################################
    ############ END REFACTORED CODE ###################
    ####################################################

    /**
     * Search to five offer
     * @param string $keyword
     * @param boolean $flag
     * @version 1.0
     * @return array $data
     * @author Er.kundal
     */
    public static function searchTopTenOffer($keyword, $flag)
    {
        $lastdata=self :: getsplpage();

        if(sizeof($lastdata)>0){
            for($i=0;$i<sizeof($lastdata);$i++){
                $codevalues[$i]=$lastdata[$i]['specialpageId'];
            }

            $codevalues = implode(",", $codevalues);

        }else{
            $codevalues = '0';
        }

        $data = Doctrine_Query::create()
        ->select('p.pageTitle as title')
        ->from("page p")
        ->where('p.deleted=0')
        ->andWhere('p.pageType="offer"')
        ->andWhere("p.pageTitle LIKE ?", "$keyword%")
        ->andWhere("p.id NOT IN ($codevalues)")
        ->limit(10)->fetchArray();

        return $data;
    }

        /**
         * get Special offer list from database
         * @author Er.kundal
         * @version 1.0
         * @return array $data
         */
         public static function getsplpage()
         {
            $data = Doctrine_Query::create()
            ->select('sp.type,sp.position,sp.specialpageId,p.pageTitle as title')
            ->from('SpecialList sp')->leftJoin('sp.page p')
            ->orderBy('sp.position ASC')->fetchArray();
            return $data;
        }
                    /**
                    * add offer in Special offer
                    * @author Er.kundal
                    * @version 1.0
                    * @return integer $flag
                    */
                    public static function addOfferInList($title)
                    {
                    //find offer by title
                    $title = addslashes($title);

                    $page = Doctrine_query::create()->from('Page')
                    ->where('pageTitle=' . "'$title'")->limit(1)->fetchArray();
                    $flag = '2';

                    if (sizeof($page) > 0) {

                            //check offer exist or not
                            $pc = Doctrine_Core::getTable('SpecialList')
                            ->findBy('specialpageId', $page[0]['id']);

                            if (sizeof($pc) > 0) {

                            } else {

                            $flag = '1';
                            //find last postion  from database
                            $data = Doctrine_Query::create()->select('p.position')
                            ->from('SpecialList p')->orderBy('p.position DESC')
                            ->limit(1)->fetchArray();
                            if(sizeof($data) > 0){

                            $NewPos = $data[0]['position'];

                            } else {

                            $NewPos = 1;
                            }				//add new offer if not exist in datbase
                                $pc = new SpecialList();
                                $pc->type = 'MN';
                                $pc->status = '1';
                                $pc->specialpageId = $page[0]['id'];
                                $pc->position = (intval($NewPos) + 1);
                                $pc->save();
                                $flag = $pc->toArray();
                            }

                            }
                            //call cache function
                            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPagesHome_list');

                            return $flag;

                            }
        /**
        * delete Special offer
        * @param integer $id
        * @param integer $position
        * @author Er.kundal
        * @version 1.0
        */
        public static function deletePapularCode($id, $position)
        {
            if ($id) {

                //delete Special list offer from Special list
                    $pc = Doctrine_Query::create()->delete('SpecialList')
                    ->where('id=' . $id)->execute();
                    //change position by 1 of each below element
                    $q = Doctrine_Query::create()->update('SpecialList p')
                    ->set('p.position', 'p.position -1')
                    ->where('p.position >' . $position)->execute();
                    //call cache function
                    FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPagesHome_list');
                    return true ;
            }

            return false ;
        }

        /**
        * move up Special offer from list
        * @param integer $id
        * @param integer $position
        * @author Er.kundal
        * @version 1.0
        */
        public static function moveUpSpecial($id, $position)
        {
                $pos = (intval($position) - 1);


                //find prev element from database based of current
                $PrevPc = Doctrine_Core::getTable('SpecialList')
                ->findBy('position', $pos)->toArray();
                //change position of prev element with current
                //$flag =  1;
            if(count($PrevPc) > 0) {

                //$flag =2;
                $changePrevPc = Doctrine_Core::getTable('SpecialList')
                ->find($PrevPc[0]['id']);
                $changePrevPc->position = $position;
                $changePrevPc->save();
                //change position of current element with postition + 1
                $pc = Doctrine_Core::getTable('SpecialList')->find($id);
                $pc->position = $pos;
                $pc->save();
                //call cache function
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPagesHome_list');

                return true ;
            }

            return false ;
                //return $flag;
        }
        /**
        * move down Special offer from list
        * @param integer $id
        * @param integer $position
        * @author kraj
        * @version 1.0
        */
        public static function moveDownSpecial($id, $position)
        {
            $pos = (intval($position) + 1);
            //find next element from database based of current
            $PrevPc = Doctrine_Core::getTable('SpecialList')
            ->findBy('position', $pos)->toArray();
            //change position of next element with current
            if(count($PrevPc) > 0) {

                    $changePrevPc = Doctrine_Core::getTable('SpecialList')
                    ->find($PrevPc[0]['id']);
                    $changePrevPc->position = $position;
                    $changePrevPc->save();
                    //change position of current element with postition - 1
                    $pc = Doctrine_Core::getTable('SpecialList')->find($id);
                    $pc->position = $pos;
                    $pc->save();
                    //call cache function
                    FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPagesHome_list');

                    return true ;
            }
            return false ;
        }

}