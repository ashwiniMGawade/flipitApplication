<?php
use \Core\Domain\Factory\AdminFactory;
use \Core\Service\Errors;

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
        $conditions = array(
            'deleted' => 0,
            'status' => 1
        );

        $order = array(
            'name' => 'ASC'
        );
        $this->view->shops = AdminFactory::getShops()->execute($conditions, $order);

        if ($this->_request->isPost()) {
            $landingPage = AdminFactory::createLandingPage()->execute();
            $shopId = intval(FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('selectedShop')));
            if ($shopId <= 0) {
                $this->view->landingPage = $this->getAllParams();
                $this->setFlashMessage('error', 'Invalid Shop');
            } else {
                $shop = AdminFactory::getShop()->execute($shopId);

                $parameters['status'] = intval(FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('status')));
                $parameters['title'] = FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('title'));
                $parameters['permalink'] = FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('permalink'));
                $parameters['subTitle'] = FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('subTitle'));
                $parameters['metaTitle'] = FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('overwriteTitle'));
                $parameters['metaDescription'] = FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('metaDescription'));
                $parameters['content'] = FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('pageContent'));
                $parameters['shop'] = $shop;

                $result = AdminFactory::addLandingPage()->execute($landingPage, $parameters);

                if ($result instanceof Errors) {
                    $this->view->landingPage = $this->getAllParams();
                    $errors = $result->getErrorsAll();
                    $this->setFlashMessage('error', $errors);
                } else {
                    $this->setFlashMessage('success', 'Landing Page has been added successfully');
                    $this->redirect(HTTP_PATH.'admin/landingpages');
                }
            }
        }
    }

    public function deleteAction()
    {
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
