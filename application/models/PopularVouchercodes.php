<?php

/**
 * PopularVouchercodes
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##Er.Kundal## <##Er.Kundal##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
class PopularVouchercodes extends BasePopularVouchercodes
{

    public static function searchTopTenOffer($keyword, $flag)
    {
        $lastdata=self :: getPopularvoucherCode();

        if(sizeof($lastdata)>0){
            for($i=0;$i<sizeof($lastdata);$i++){
                $codevalues[$i]=$lastdata[$i]['vaoucherofferId'];
            }

            $codevalues = implode(",", $codevalues);

        }else{
            $codevalues = '0';
        }


        $offer = Doctrine_Query::create()
        ->select()
        ->from("PopularCode p")
        ->leftJoin('p.offer offer')
        ->where('offer.deleted=0')
        ->andWhere('offer.offline = 0')
        ->andWhere("offer.title LIKE ?", "$keyword%")
        ->andWhere("offer.id NOT IN ($codevalues)")
        ->limit(10)->fetchArray();

        return $offer;
    }


    /**
     * get popular offer for voucher code list from database
     * @author Er.Kundal
     * @version 1.0
     * @return array $data
     */
    public static function getPopularvoucherCode()
    {
        $data = Doctrine_Query::create()
                ->select('p.id,o.title,p.type,p.position,p.vaoucherofferId')
                ->from('PopularVouchercodes p')->leftJoin('p.offer o')
                ->orderBy('p.position ASC')->fetchArray();
        return $data;

    }

    /**
     * add offer in Popular voucher code
     * @author Er.Kundal
     * @version 1.0
     * @return integer $flag
     */
    public static function addOfferInVouchercode($title)
    {
        //find offer by title
        $title = addslashes($title);
        $Offer = Doctrine_query::create()->from('Offer')
        ->where('title=' . "'$title'")->limit(1)->fetchArray();
        $flag = '2';


        if (sizeof($Offer) > 0) {

            //check offer exist or not
            $pc = Doctrine_Core::getTable('PopularVouchercodes')
            ->findBy('vaoucherofferId', $Offer[0]['id']);

            if (sizeof($pc) > 0) {
            } else {

                $flag = '1';
                //find last postion  from database
                $data = Doctrine_Query::create()->select('p.position')
                ->from('PopularVouchercodes p')->orderBy('p.position DESC')
                ->limit(1)->fetchArray();
                if(sizeof($data) > 0){

                    $NewPos = $data[0]['position'];

                } else {

                    $NewPos = 1;
                }				//add new offer if not exist in datbase
                $pc = new PopularVouchercodes();
                $pc->type = 'MN';
                $pc->vaoucherofferId = $Offer[0]['id'];
                $pc->position = (intval($NewPos) + 1);
                $pc->save();
                $flag = $pc->toArray();
            }

        }
        //call cache function
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('top_20_offers_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list_feed');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list_shoppage');
        return $flag;

    }


    /**
     * delete popular vocher code
     * @param integer $id
     * @param integer $position
     * @author Er.kundal
     * @version 1.0
     */
    public static function deletePapularvocherCode($id, $position)
    {
        if ($id) {
            //delete popular code from list
            $pc = Doctrine_Query::create()->delete('PopularVouchercodes')
            ->where('id=' . $id)->execute();
            //change position by 1 of each below element
            $q = Doctrine_Query::create()->update('PopularVouchercodes p')
            ->set('p.position', 'p.position -1')
            ->where('p.position >' . $position)->execute();
            //call cache function
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('top_20_offers_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list_feed');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list_shoppage');

        }
    }


    /**
     * move up popular code from list
     * @param integer $id
     * @param integer $position
     * @author Er.kundal
     * @version 1.0
     */
    public static function moveUpCode($id, $position)
    {
        $pos = (intval($position) - 1);
        //find prev element from database based of current
        $PrevPc = Doctrine_Core::getTable('PopularVouchercodes')
        ->findBy('position', $pos)->toArray();
        //change position of prev element with current
        //$flag =  1;
        if(count($PrevPc) > 0) {

            //$flag =2;
            $changePrevPc = Doctrine_Core::getTable('PopularVouchercodes')
            ->find($PrevPc[0]['id']);
            $changePrevPc->position = $position;
            $changePrevPc->save();
            //change position of current element with postition + 1
            $pc = Doctrine_Core::getTable('PopularVouchercodes')->find($id);
            $pc->position = $pos;
            $pc->save();

        }
        //call cache function
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('top_20_offers_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list_feed');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list_shoppage');

        //return $flag;
    }

    /**
     * move down popular voucher code from list
     * @param integer $id
     * @param integer $position
     * @author Er.kundal
     * @version 1.0
     */
    public static function moveDownCode($id, $position)
    {
        $pos = (intval($position) + 1);
        //find next element from database based of current
        $PrevPc = Doctrine_Core::getTable('PopularVouchercodes')
        ->findBy('position', $pos)->toArray();
        //change position of next element with current
        if(count($PrevPc) > 0) {

            $changePrevPc = Doctrine_Core::getTable('PopularVouchercodes')
            ->find($PrevPc[0]['id']);
            $changePrevPc->position = $position;
            $changePrevPc->save();
            //change position of current element with postition - 1
            $pc = Doctrine_Core::getTable('PopularVouchercodes')->find($id);
            $pc->position = $pos;
            $pc->save();

        }
        //call cache function
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('top_20_offers_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list_feed');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list_shoppage');

    }

    /*********************************************Front fuction for displaying offers/code********************************************/

    /**
     * get popular offer for voucher code list from database for front end
     * @author Er.Kundal
     * @version 1.0
     * @return array $data
     */
    public static function gethomePopularvoucherCode($flag)
    {
        $data = Doctrine_Query::create()
        ->select('p.id,o.title,o.shopid,o.offerlogoid,o.couponcode,o.exclusivecode,o.discount,o.discountvalueType,s.name,s.permaLink,s.logoid,l.path,l.name,p.type,p.position,p.vaoucherofferId')
        ->from('PopularVouchercodes p')
        ->leftJoin('p.offer o')
        ->leftJoin('o.shop s')
        ->leftJoin('s.logo l')
        ->where('o.deleted =0')
        ->andWhere('s.deleted=0')
        ->orderBy('p.position ASC')->limit($flag)->fetchArray();

        return $data;
    }

    /**
     * Get newest offer for home page list from database
     * @author Er.Kundal
     * @version 1.0
     * @return array $data
     */
    public static function getNewstoffer($flag)
    {
        $date = date('Y-m-d H:i:s');
        //$memOnly = "MEM";
        $data = Doctrine_Query::create()

        ->select('o.title,o.Visability,o.couponCodeType,o.discountType,o.couponCode,o.exclusiveCode,o.editorPicks,o.discount,o.startdate,o.enddate,o.discountvalueType,s.name,s.permaLink,s.views,l.*')
        ->from("Offer o")
        ->leftJoin('o.shop s')
        ->leftJoin('s.logo l')
        ->where('o.Visability!="MEM"')
        ->andWhere('o.discounttype="CD"')
        ->andWhere("(couponCodeType = 'UN' AND (SELECT count(id)  FROM CouponCode WHERE offerid = o.id and status=1)  > 0) or couponCodeType = 'GN'")
        ->andWhere('s.status = 1')
        ->andWhere('o.deleted =0')
        ->andWhere("o.userGenerated = 0")
        ->andWhere('o.offline = 0')
        ->andWhere('o.enddate > "'.$date.'"')
        ->andWhere('o.startdate <= "'.$date.'"')
        ->andWhere('s.deleted=0')
        ->orderBy("o.startdate DESC")
        ->limit($flag)
        ->fetchArray();
        return $data;

    }


    /** get Special offer list from database
     /* get Special offer list from database
     * @author Er.kundal
     * @version 1.0
     * @return array $data
     */
    public static function getSpecialoffer($flag)
    {
        $data = Doctrine_Query::create()
        ->select('p.id,p.type,p.position,p.specialOfferId,o.title,o.Visability,o.discountType,o.exclusiveCode,o.discount,o.discountvalueType,s.name,s.permaLink,s.views,c.name,c.permaLink,i.*')
        ->from('SpecialList p')
        ->leftJoin('p.offer o')
        ->leftJoin('o.shop s')
        ->leftJoin('o.category c')
        ->leftJoin('s.logo i')
        ->where('s.deleted=0')
        ->orderBy('p.position ASC')->limit($flag)->fetchArray();
        return $data;

    }






}
