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



            # if top korting are less than 15 then add newest code to fill up the list upto 15
            if(count($topVouchercodes) < 15 )
             {
                # the limit of popular oces
                $additionalCodes = 15 - count($topVouchercodes) ;

                # GET TOP 5 POPULAR CODE
                $additionalTopVouchercodes = $offers = Offer::commongetnewestOffers('newest', $additionalCodes);


                foreach ($additionalTopVouchercodes as $key => $value) {

                    $topVouchercodes[] =     array('id'=> $value['shop']['id'],
                                                    'permalink' => $value['shop']['permalink'],
                                                    'offer' => $value
                                                  );
                }
             }


    		FrontEnd_Helper_viewHelper::setInCache('all_popularvaouchercode_list_feed', $topVouchercodes);
    		
    	} else {
    		 
    		$topVouchercodes = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_popularvaouchercode_list_feed');
    	}
    	
    	$this->view->topCode = $topVouchercodes;
    }


}

