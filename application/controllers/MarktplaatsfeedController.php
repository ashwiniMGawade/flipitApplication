<?php

class MarktplaatsfeedController extends Zend_Controller_Action 
{

    public function init()
    {
        /* Initialize action controller here */
    	
    	$this->_helper->layout()->disableLayout();
    }

    public function indexAction()
    {
     	
 	  	# fetch Popular voucher offers KORTINGSCODES list 
    	
    	$voucherflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_popularvaouchercode_list_feed');
    	
    	//key not exist in cache
    	if($voucherflag){
    		$topVouchercodes = PopularCode::gethomePopularvoucherCodeForMarktplaatFeeds(15);
    		FrontEnd_Helper_viewHelper::setInCache('all_popularvaouchercode_list_feed', $topVouchercodes);
    	} else {
    		
    		$topVouchercodes = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_popularvaouchercode_list_feed');
    	}
    	
    	# fetch money saving article list 
    	$moneyflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_homemanisaving_list');
    	
    	//key not exist in cache
    	if($moneyflag){
    		$moneySaving = FrontEnd_Helper_viewHelper::gethomeSections("moneySaving", 2);
    		FrontEnd_Helper_viewHelper::setInCache('all_homemanisaving_list', $moneySaving);
    	} else {
    		$moneySaving = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_homemanisaving_list');
    	}
    	
    	
    	# fetch category list
    	$categoryflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_popularcategory_list');
    	
    	//key not exist in cache
    	if($categoryflag){
    		$topCategories = FrontEnd_Helper_viewHelper::gethomeSections("category", 10);
    		FrontEnd_Helper_viewHelper::setInCache('all_popularcategory_list', $topCategories);
    	} else {
    		$topCategories = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_popularcategory_list');
    	}
    	 
    	 
    	$this->view->topCode = $topVouchercodes;
    	$this->view->moneySaving = $moneySaving;
    	$this->view->topCategories = $topCategories;
    	
    	
    	
    }


}

