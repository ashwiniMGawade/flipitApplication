<?php

class Admin_Top50couponsController extends Application_Admin_BaseController
{
    public function preDispatch()
    {
        $params = $this->_getAllParams();
        if (!\Auth_StaffAdapter::hasIdentity()) {
            $referer = new \Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }
        $this->view->controllerName = $this->getRequest()
                ->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');
    }

    public function init()
    {
    }

    public function indexAction()
    {
        $data = \KC\Repository\PopularCode::getPopularCode(50, 'TOP50');
        $neAr = array();
        foreach ($data as $pOffer) {
            $neAr[] = $pOffer['offerId'];
        }
        $allOffer = \KC\Repository\PopularCode::searchAllOffer($neAr);
        $this->view->data = $data;
        $this->view->offer = $allOffer;
    }

    public function savetop50couponspositionAction()
    {
        \KC\Repository\PopularCode::savePopularOffersPosition($this->getRequest()->getParam('offerid'), 'TOP50');
        $popularCode = \KC\Repository\PopularCode::getPopularCode(50, 'TOP50');
        self::updateVarnish();
        echo \Zend_Json::encode($popularCode);
        exit();
    }

    public function addofferAction()
    {
        $data = $this->getRequest()->getParam('id');
        $flag = \KC\Repository\PopularCode::addOfferInList($data, 'TOP50');
        if ($flag && $flag != "0" && $flag != "1" && $flag != '2') {
            self::updateVarnish();
        }
        echo \Zend_Json::encode($flag);
        die();
    }

    public function deletetop50couponAction()
    {
        $id = $this->getRequest()->getParam('id');
        $position = $this->getRequest()->getParam('pos');
        $isUpdated = \KC\Repository\PopularCode::deletePapularCode($id, $position, 'TOP50');
        if ($isUpdated) {
            self::updateVarnish();
        }
        $data = \KC\Repository\PopularCode::getPopularCode();
        echo \Zend_Json::encode($data);
        die();
    }

    public function updateVarnish()
    {
        // Add urls to refresh in Varnish
        $varnishObj = new KC\Repository\Varnish();
        $varnishObj->addUrl(rtrim(HTTP_PATH_FRONTEND, '/'));
        if (LOCALE == '') {
            $varnishObj->addUrl(HTTP_PATH_FRONTEND  . 'marktplaatsfeed');
            $varnishObj->addUrl(HTTP_PATH_FRONTEND . 'marktplaatsmobilefeed');
            $varnishObj->addUrl(HTTP_PATH_FRONTEND . 'top-50');
            $varnishObj->addUrl(HTTP_PATH_FRONTEND . 'top50');
        }
    }
}

