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

    		$topVouchercodes = Offer::getTopKortingscodeForShopPage(array(),20);



            # if top korting are less than 20 then add newest code to fill up the list upto 20
            if(count($topVouchercodes) < 20 )
             {
                # the limit of popular oces
                $additionalCodes = 20 - count($topVouchercodes) ;

                # GET TOP 5 POPULAR CODE
                $additionalTopVouchercodes = $offers = Offer::commongetnewestOffers('newest', $additionalCodes);


                foreach ($additionalTopVouchercodes as $key => $value) {

                    $topVouchercodes[] =     array('id'=> $value['shop']['id'],
                                                    'permalink' => $value['shop']['permalink'],
                                                    'offer' => $value
                                                  );
                }
             }


    		FrontEnd_Helper_viewHelper::setInCache('top_20_popularvaouchercode_list', $topVouchercodes);

    	} else {

    		$topVouchercodes = FrontEnd_Helper_viewHelper::getFromCacheByKey('top_20_popularvaouchercode_list');
    	}




    	$this->view->topCode = $topVouchercodes;

    }

}

