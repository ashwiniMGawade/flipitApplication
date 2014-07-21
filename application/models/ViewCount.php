<?php

/**
 * ViewCount
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
class ViewCount extends BaseViewCount
{
    ##########################################
    ########### REFACTORED CODE ##############
    ##########################################
    public static function getOfferClick($offerId, $clientIp)
    {
        $offerClick = Doctrine_Query::create()
            ->select('count(v.id) as exists')
            ->addSelect("(SELECT  id FROM ViewCount  click WHERE click.id = v.id) as clickId")
            ->from('ViewCount v')
            ->where('onClick!=0')
            ->andWhere('offerId="'.$offerId.'"')
            ->andWhere('IP="'.$clientIp.'"')
            ->fetchArray();
        return $offerClick[0]['exists'];
    }

    public static function saveOfferClick($offerId, $clientIp)
    {
        $offerClick  = new ViewCount();
        $offerClick->offerId = $offerId;
        $offerClick->onClick = 1;
        $offerClick->onLoad = 0;
        $offerClick->IP = $clientIp;
        $offerClick->save();
        return true;
    }

    public static function getOfferOnload($offerId, $clientIp)
    {
        $offerOnload = Doctrine_Query::create()
            ->select('count(*) as exists')
            ->from('ViewCount')
            ->where('onLoad!=0')
            ->andWhere('offerId="'.$id.'"')
            ->andWhere('IP="'.$ip.'"')
            ->fetchArray();
        return $offerOnload[0]['exists'];
    }

    public static function saveOfferOnload($offerId, $clientIp)
    {
        $offerOnload  = new ViewCount();
        $offerOnload->offerId = $offerId;
        $offerOnload->onLoad = 1;
        $offerOnload->onClick = 0;
        $offerOnload->IP = $clientIp;
        $offerOnload->save();
        return true;
    }
    ##########################################
    ########### END REFACTORED CODE ##########
    ##########################################
    /*============================Function for front-end ======================= */
    /**
     * get store from database according to type
     * @author Raman
     * @param string $offerType
     * @param integer $limit
     * @return array $data
     */
    public static function getOfferForFrontEnd($offerType,$limit=10)
    {
        $data = '';
        switch(strtolower($offerType)) {

            case 'popular':
                $format = 'Y-m-j H:m:s`	';
                $date = date($format);
                // - 4 days from today
                $past4Days = date($format, strtotime('-4 day' . $date));
                $nowDate = $date;
                //get popular offers

                $data = Doctrine_Query::create()
                ->select('vc.id, sum(vc.onclick) as views, o.id, o.title, o.visability, o.couponcode, o.refofferurl, o.startdate, o.enddate, o.exclusivecode, o.editorpicks,o.extendedoffer,o.discount, o.authorId, o.authorName, o.shopid, o.offerlogoid, o.userGenerated, o.approved,img.*')
                ->from('viewcount vc')
                ->leftJoin('vc.offer o')
                ->leftJoin('o.logo img')
                ->where('vc.updated_at <=' . "'$nowDate' AND vc.updated_at >=". "'$past4Days'")
                ->groupby('vc.offerid')
                ->orderBy('views desc')
                ->limit($limit)->fetchArray();
                //die;
                break;

            default:

                break;
        }
        //echo "<pre>";
        //print_r($data);
        //exit;
        return $data;
    }


    /**
     * generate papular code by formula
     * @author kraj modified by Raman
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
        $NewpapularCode = Doctrine_Query::create()->select('v.id,v.offerId, ((sum(v.onclick)) / (DATEDIFF(NOW(),o.startdate))) as pop, o.startdate')
        ->from('ViewCount v')
        ->where('v.updated_at <=' . "'$nowDate' AND v.updated_at >="
                . "'$past4Days'")
                ->leftJoin('v.offer o')
        ->groupBy('v.offerId')->orderBy('pop DESC')->limit(10)
                ->fetchArray();

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
            //die();
    }

    /**
     * get popular code list from database
     * @author Raman
     * @version 1.0
     * @return array $data
     */
    public static function frontendGetPopularCode()
    {
        $data = Doctrine_Query::create()
        ->select('p.id,o.title,p.type,p.position,p.offerId, o.id, o.title, o.visability, o.couponcode, o.refofferurl, o.startdate, o.enddate, o.exclusivecode, o.editorpicks,o.extendedoffer,o.discount, o.authorId, o.authorName, o.shopid, o.offerlogoid, o.userGenerated, o.approved,img.id, img.path, img.name')
        ->from('PopularCode p')
        ->leftJoin('p.offer o')
        ->leftJoin('o.logo img')
        ->orderBy('p.position ASC')->fetchArray();
        return $data;

    }

    /**
     * getClickId returns the click record data
     * @param integer $offerId related offer id
     * @param long $ip ip address of user ip address (converted by ip2long fucntion)
     * @return array
     * @author Surinderpal Singh
     *
     *
     */
    public static function getClickId($offerId , $ip)
    {



        return  Doctrine_Query::create()
                ->select("id, subId")
                ->from("ViewCount v")
                ->where("v.ip = ?" , $ip)
                ->andWhere("v.offerId = ? " , $offerId)
                ->andWhere(".onclick = 1")
                ->fetchOne(null, Doctrine::HYDRATE_ARRAY) ;

    }

    /**
     * get No of Clickouts in last 7 days for dashboard
     * @author Raman
     * @return integer
     * @version 1.0
     */
    public static function getAmountClickoutsLastWeek()
    {

        $format = 'Y-m-j H:i:s';
        $date = date($format);
        // - 7 days from today
        $past7Days = date($format, strtotime('-7 day' . $date));

        $data = Doctrine_Query::create()
            ->select("count(*) as amountclickouts")
            ->from("ViewCount v")
            ->where('v.created_at BETWEEN "'.$past7Days.'" AND "'.$date.'"')
            ->andWhere("v.onclick = 1")
            ->fetchOne(null, Doctrine::HYDRATE_ARRAY);

        return $data;
    }


    /**
     * processViewCount
     *
     * it will set view count value 1 which mean it is already counted
     * @param integr $id offer id
     */

    public static function processViewCount($id = null)
    {


        $query = Doctrine_Query::create()
                ->update('ViewCount v')
                ->set('v.counted' , 1 )
                ->where('v.counted = 0');

        # set counted against given id
        if($id){

            $query = $query->andWhere('v.offerId =  ? ' , $id);
        }

        $query->execute();

    }

}
