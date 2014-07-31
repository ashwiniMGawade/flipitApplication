<?php

class SargassofeedController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */

        $this->_helper->layout()->disableLayout();
    }

    public function indexAction()
    {

        # fetch top 20 Popular offers
        $voucherflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('top20_offers_list');

        if($voucherflag){

            # get top 20 vouchercodes
            $topVouchercodes = Offer::getTopCouponCodes(array(),20);
            $topVouchercodes =  FrontEnd_Helper_viewHelper::fillupTopCodeWithNewest($topVouchercodes ,20);

            FrontEnd_Helper_viewHelper::setInCache('top20_offers_list', $topVouchercodes);

        } else {

            $topVouchercodes = FrontEnd_Helper_viewHelper::getFromCacheByKey('top20_offers_list');
        }




        $this->view->topCode = $topVouchercodes;

    }

}
