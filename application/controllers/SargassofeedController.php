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
        $voucherflag =  \FrontEnd_Helper_viewHelper::checkCacheStatusByKey('20_topOffers_list');

        if($voucherflag){

            # get top 20 vouchercodes
            $topVouchercodes = \KC\Repository\Offer::getTopCouponCodes(array(),20);
            $topVouchercodes =  \FrontEnd_Helper_viewHelper::fillupTopCodeWithNewest($topVouchercodes ,20);

            \FrontEnd_Helper_viewHelper::setInCache('20_topOffers_list', $topVouchercodes);

        } else {

            $topVouchercodes = \FrontEnd_Helper_viewHelper::getFromCacheByKey('20_topOffers_list');
        }




        $this->view->topCode = $topVouchercodes;

    }

}
