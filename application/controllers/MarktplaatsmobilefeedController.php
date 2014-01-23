<?php

class MarktplaatsmobilefeedController extends Zend_Controller_Action 
{

    public function init()
    {
        /* Initialize action controller here */
    	
    	$this->_helper->layout()->disableLayout();
    }

    public function indexAction()
    {
     	
    	# fetch Popular voucher offers KORTINGSCODES list
    	$voucherflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_popularvaouchercode_list');
    	 
    	//key not exist in cache
    	if($voucherflag){
    		
    		$topVouchercodes = PopularCode::gethomePopularvoucherCodeForMarktplaatFeeds(15);
    		FrontEnd_Helper_viewHelper::setInCache('all_popularvaouchercode_list_feed', $topVouchercodes);
    		
    	} else {
    		 
    		$topVouchercodes = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_popularvaouchercode_list_feed');
    	}
    	
    	$this->view->topCode = $topVouchercodes;
    }


}

