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
    	$voucherflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('top_20_popularvaouchercode_list');

    	if($voucherflag){

            # get top 20 vouchercodes
            $topVouchercodes = Offer::getTopKortingscodeForShopPage(array(),20);
            $topVouchercodes =  FrontEnd_Helper_viewHelper::fillupTopCodeWithNewest($topVouchercodes ,20);

    		FrontEnd_Helper_viewHelper::setInCache('top_20_popularvaouchercode_list', $topVouchercodes);

    	} else {

    		$topVouchercodes = FrontEnd_Helper_viewHelper::getFromCacheByKey('top_20_popularvaouchercode_list');
    	}




    	$this->view->topCode = $topVouchercodes;

    }

}

