<?php

use \Core\Domain\Factory\AdminFactory;

class Admin_LandingpagesController extends Application_Admin_BaseController
{
    public function preDispatch()
    {
        if (!Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->redirect('/admin/auth/index');
        }
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');
        $sessionNamespace = new Zend_Session_Namespace();
        if ($sessionNamespace->settings['rights']['administration']['rights'] != '1') {
            $this->redirect('/admin');
        }
    }

    public function init()
    {
    }

    public function indexAction()
    {
    }

    public function getAction()
    {
        $landingPages = AdminFactory::getLandingPages()->execute();
        $landingPagesData = array();
        foreach ($landingPages as $landingPage) {
            $landingPagesData[] = array(
                'id' => $landingPage->getId(),
                'title' => $landingPage->getTitle(),
                'shopName' => $landingPage->getShop()->getName(),
                'permalink' => $landingPage->getPermalink(),
                'shopOffersCount' => $landingPage->getShop()->getOfferCount(),
                'shopClickoutCount' => $landingPage->getShop()->getShopAndOfferClickouts(),
                'status' => $landingPage->getStatus(),
                'offlineSince' => $landingPage->getOfflineSince()
            );
        }

        $response = \DataTable_Helper::createResponse(1, $landingPagesData, count($landingPages));
        echo Zend_Json::encode($response);
        exit;
    }

    public function createAction()
    {
    }

    public function deleteAction() {
        $landingPageId = intval($this->getRequest()->getParam('id'));
        if (intval($landingPageId) < 1) {
            $this->setFlashMessage('error', 'Invalid page id provided.');
            die;
        }

        $result = AdminFactory::getLandingPage()->execute(array('id' => $landingPageId));
        if ($result instanceof Errors) {
            $errors = $result->getErrorsAll();
            $this->setFlashMessage('error', $errors);
            die;
        }
        AdminFactory::deleteLandingPage()->execute($result);
        $this->setFlashMessage('success', 'Landing page deleted successfully.');
        die;
    }
}
