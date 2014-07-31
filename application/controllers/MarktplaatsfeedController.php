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

        $voucherflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_popularVoucherCodesList_feed');

        //key not exist in cache
        if ($voucherflag) {

            # get top 15 vouchercodes
            $topVouchercodes = PopularCode::gethomePopularvoucherCodeForMarktplaatFeeds(15);
            $topVouchercodes =  FrontEnd_Helper_viewHelper::fillupTopCodeWithNewest($topVouchercodes, 15);

        } else {

            $topVouchercodes = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_popularVoucherCodesList_feed');
        }

        # fetch money saving article list
        $moneyflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_homeMoneySaving_list');

        //key not exist in cache
        if ($moneyflag) {
            $moneySaving = FrontEnd_Helper_viewHelper::gethomeSections("moneySaving", 2);
            FrontEnd_Helper_viewHelper::setInCache('all_homeMoneySaving_list', $moneySaving);
        } else {
            $moneySaving = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_homeMoneySaving_list');
        }


        # fetch category list
        $categoryflag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_popularCategories_list');

        //key not exist in cache
        if ($categoryflag) {
            $topCategories = FrontEnd_Helper_viewHelper::gethomeSections("category", 10);
            FrontEnd_Helper_viewHelper::setInCache('all_popularCategories_list', $topCategories);
        } else {
            $topCategories = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_popularCategories_list');
        }


        $this->view->topCode = $topVouchercodes;
        $this->view->moneySaving = $moneySaving;
        $this->view->topCategories = $topCategories;



    }
}
