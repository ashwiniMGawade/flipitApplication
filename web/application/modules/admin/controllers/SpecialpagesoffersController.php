<?php

class Admin_SpecialpagesoffersController extends Application_Admin_BaseController
{
    public function preDispatch()
    {
        $dbConnection = \BackEnd_Helper_viewHelper::addConnection(); // connection
        if (!\Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }
        \BackEnd_Helper_viewHelper::closeConnection($dbConnection);
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');
    }
    
    public function indexAction()
    {
        $pageId = $this->getRequest()->getParam('pageId');
        $specialListPages = \KC\Repository\Page::getSpecialListPages();
        if (isset($pageId)) {
            $pageId = $this->getRequest()->getParam('pageId');
        } else {
            $pageId = $specialListPages[0]['id'];
        }
        $specialPageOffers = \KC\Repository\SpecialPagesOffers::getSpecialPageOfferById($pageId);
        $offerIds = array();
        foreach ($specialPageOffers as $pOffer) {
            $offerIds[] = $pOffer['offers']['id'];
        }
        $allOffer = \KC\Repository\PopularCode::searchAllOffer($offerIds, false);
        $this->view->specialPageOffers = $specialPageOffers;
        $this->view->offer = $allOffer;
        $this->view->specialPages = $specialListPages;
        $this->view->pageId = $pageId;
    }


    public function addofferAction()
    {
        $offerId = $this->getRequest()->getParam('id');
        $pageId = $this->getRequest()->getParam('pageId');
        $result = \KC\Repository\SpecialPagesOffers::addOfferInList($offerId, $pageId);
        echo Zend_Json::encode($result);
        exit();
    }
 
    public function deletecodeAction()
    {
        $id = $this->getRequest()->getParam('id');
        $position = $this->getRequest()->getParam('pos');
        $pageId = $this->getRequest()->getParam('pageId');
        $isUpdated = \KC\Repository\SpecialPagesOffers::deleteCode($id, $position, $pageId);
        $specialPageOffers = \KC\Repository\SpecialPagesOffers::getSpecialPageOfferById($pageId);
        echo Zend_Json::encode($specialPageOffers);
        exit();
    }

    public function savepositionAction()
    {
        $this->_helper->layout->disableLayout();
        $pageId = $this->getRequest()->getParam('pageId');
        \KC\Repository\SpecialPagesOffers::savePosition($this->getRequest()->getParam('offersIds'), $pageId);
        $SpecialPagesOffers = \KC\Repository\SpecialPagesOffers::getSpecialPageOfferById($pageId);
        echo Zend_Json::encode($SpecialPagesOffers);
        exit();
    }

    public function addnewoffersAction()
    {
        \KC\Repository\SpecialPagesOffers::addNewSpecialPageOffers();
        exit();
    }
}
