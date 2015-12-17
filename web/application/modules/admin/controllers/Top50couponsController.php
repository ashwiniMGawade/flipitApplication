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
        $data = \KC\Repository\PopularCode::getPopularCode();
        $neAr = array();
        foreach ($data as $pOffer) {
            $neAr[] = $pOffer['offerId'];
        }
        $allOffer = \KC\Repository\PopularCode::searchAllOffer($neAr);
        $this->view->data = $data;
        $this->view->offer = $allOffer;
    }
}
