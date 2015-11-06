<?php

/**
 * CouponCode
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
class CouponCode extends BaseCouponCode
{

    ##################################################################################
    ################## REFACTORED CODE ###############################################
    ##################################################################################

    public static function returnAvailableCoupon($id, $pageType='')
    {
        $availableCoupon = Doctrine_Query::create()
        ->select('c.code')
        ->from('CouponCode c')
        ->where("c.offerid = " . $id)
        ->andWhere('c.status=1')
        ->limit(1)
        ->fetchOne(null, Doctrine::HYDRATE_ARRAY);
        return $availableCoupon;
    }

    public static function updateCodeStatus($id, $code, $status = 0)
    {
        Doctrine_Query::create()->update('CouponCode')
        ->set('status', $status)
        ->where("code = '" . $code ."'")
        ->andWhere('offerid ='.  $id)
        ->execute();
        # refresh varnish if no codce is available
        $totalAvailCode  = Doctrine_Query::create()
        ->select('count(id)')
        ->from('CouponCode c')
        ->where("c.offerid = " . $id)
        ->andWhere('c.status=1')
        ->fetchOne(NULL, Doctrine::HYDRATE_ARRAY);

        if ($totalAvailCode['count'] == 0) {
            Offer::updateCache($id);
            $varnishObj = new Varnish();
            $varnishObj->addUrl(HTTP_PATH);
            $varnishObj->addUrl(HTTP_PATH . FrontEnd_Helper_viewHelper::__link('link_nieuw'));
            $varnishObj->addUrl(HTTP_PATH_FRONTEND . FrontEnd_Helper_viewHelper::__link('link_top-50'));
            if (LOCALE == '') {
                if (defined(HTTP_PATH_FRONTEND)) {
                    $varnishObj->addUrl(HTTP_PATH_FRONTEND  . 'marktplaatsfeed');
                    $varnishObj->addUrl(HTTP_PATH_FRONTEND . 'marktplaatsmobilefeed');
                } else {
                    $varnishObj->addUrl(HTTP_PATH  . 'marktplaatsfeed');
                    $varnishObj->addUrl(HTTP_PATH . 'marktplaatsmobilefeed');
                }
            }
            $varnishUrls = Offer::getAllUrls($id);

            if (isset($varnishUrls) && count($varnishUrls) > 0) {
                foreach ($varnishUrls as $varnishValue) {
                    $varnishObj->addUrl(HTTP_PATH . $varnishValue);
                }
            }
        }
    }
    ##################################################################################
    ################## END REFACTORED CODE ###########################################
    ##################################################################################


    /**
     * get list of coupon code for export
     * @author sp singh
     * @param integer $id offerid
     * @return array $code
     * @version 1.0
     */
    public static function exportCodeList($id)
    {
        $codeList = Doctrine_Query::create()
            ->select('c.code,c.status')
            ->from("CouponCode c")
            ->where("c.offerid = ?" ,$id)
            ->fetchArray();


        return $codeList;

    }

    /**
     * returnCodesDetail
     *
     * return detrail of codes like as total code,used,availalbe
     *
     * @param integer $id offer id
     * @return array
     *
     * @author spsingh
     *
     */
    public static function returnCodesDetail($id)
    {
            $data = Doctrine_Query::create()
            ->select('count(c.id) as total')
                ->from('CouponCode c')
                ->addSelect("(SELECT  count(cc.status) FROM CouponCode cc WHERE cc.offerid = c.offerid and cc.status = 0) as used")
                ->addSelect("(SELECT  count(ccc.status) FROM CouponCode ccc WHERE ccc.offerid = c.offerid and ccc.status = 1) as available")
                ->where("c.offerid = " . $id )
                ->fetchOne(null, Doctrine::HYDRATE_ARRAY);

            return $data;


    }




}
