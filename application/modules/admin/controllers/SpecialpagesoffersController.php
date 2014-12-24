<?php

class Admin_SpecialpagesoffersController extends Zend_Controller_Action
{
    public function preDispatch()
    {
        $dbConnection = BackEnd_Helper_viewHelper::addConnection(); // connection
        if (!Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }
        BackEnd_Helper_viewHelper::closeConnection($dbConnection);
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');
    }
    
    public function indexAction()
    {
        $pageId = $this->getRequest()->getParam('pageId');
        $specialListPages = Page::getSpecialListPages();
        if (isset($pageId)) {
            $pageId = $this->getRequest()->getParam('pageId');
        } else {
            $pageId = $specialListPages[0]['id'];
        }
        $specialPageOffers = SpecialPagesOffers::getSpecialPageOfferById($pageId);
        $offerIds = array();
        foreach ($specialPageOffers as $pOffer) {
            $offerIds[] = $pOffer['offerId'];
        }
        $allOffer = PopularCode::searchAllOffer($offerIds);
        $this->view->specialPageOffers = $specialPageOffers;
        $this->view->offer = $allOffer;
        $this->view->specialPages = $specialListPages;
        $this->view->pageId = $pageId;
    }


    public function addofferAction()
    {
        $offerId = $this->getRequest()->getParam('id');
        $pageId = $this->getRequest()->getParam('pageId');
        $result = SpecialPagesOffers::addOfferInList($offerId, $pageId);
        echo Zend_Json::encode($result);
        exit();
    }
 
    public function deletecodeAction()
    {
        $id = $this->getRequest()->getParam('id');
        $position = $this->getRequest()->getParam('pos');
        $pageId = $this->getRequest()->getParam('pageId');
        $isUpdated = SpecialPagesOffers::deleteCode($id, $position, $pageId);
        $specialPageOffers = SpecialPagesOffers::getSpecialPageOfferById($pageId);
        echo Zend_Json::encode($specialPageOffers);
        exit();
    }

    public function savepositionAction()
    {
        $pageId = $this->getRequest()->getParam('pageId');
        SpecialPagesOffers::savePosition($this->getRequest()->getParam('offersIds'), $pageId);
        $popularArticles = SpecialPagesOffers::getSpecialPageOfferById($pageId);
        echo Zend_Json::encode($popularArticles);
        exit();
    }
}