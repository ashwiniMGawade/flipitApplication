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
        $voucherflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('10_popularShopsForHomePage_list');

        //key not exist in cache
        if ($voucherflag) {

            # get top 15 vouchercodes
            $topVouchercodes = PopularCode::gethomePopularvoucherCodeForMarktplaatFeeds(15);
            $topVouchercodes =  FrontEnd_Helper_viewHelper::fillupTopCodeWithNewest($topVouchercodes, 15);

            FrontEnd_Helper_viewHelper::setInCache('all_popularVoucherCodesList_feed', $topVouchercodes);

        } else {

            $topVouchercodes = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_popularVoucherCodesList_feed');
        }

        $this->view->topCode = $topVouchercodes;
    }


}
