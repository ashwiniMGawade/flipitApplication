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
        $this->view->shops = \KC\Repository\Shop::getOfferShopList();

        if ($this->_request->isPost()) {
            $landingPage = AdminFactory::createLandingPage()->execute();
            $shopId = intval(FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('selectedShop')));
            if ($shopId <= 0) {
                $this->view->landingPage = $this->getAllParams();
                $this->setFlashMessage('error', 'Invalid Shop');
            } else {
                $shop = AdminFactory::getShop()->execute($shopId);

                $conditions = array(
                    'shop' => $shop
                );
                $landingPagesForShop = AdminFactory::getLandingPages()->execute($conditions);

                if (count($landingPagesForShop) > 0) {
                    $this->view->landingPage = $this->getAllParams();
                    $this->setFlashMessage('error', 'Landing Page already exists for '.$shop->getName().'. Please select a different shop.');
                } else {
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
    }

    public function editAction()
    {
        $conditions = array(
            'deleted' => 0,
            'status' => 1
        );

        $order = array(
            'name' => 'ASC'
        );
        $this->view->shops = \KC\Repository\Shop::getOfferShopList();

        $landingPageId = intval(FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('id')));
        $this->view->qstring = $_SERVER['QUERY_STRING'];
        if ($landingPageId <= 0) {
            $this->setFlashMessage('error', 'Invalid Landing Page Id provided.');
            $this->redirect(HTTP_PATH.'admin/landingpages');
        }
        $result = AdminFactory::getLandingPage()->execute(array('id' => $landingPageId));
        if ($result instanceof Errors) {
            $errors = $result->getErrorsAll();
            $this->setFlashMessage('error', $errors);
            $this->redirect(HTTP_PATH.'admin/landingpages');
        } else {
            $landingPage = $result;
            $this->view->id = $landingPageId;
            $permalink = $landingPage->getPermalink();
            $shop = $landingPage->getShop();
            $shopId = $shop->getId();

            $landingPageInfo = array(
                'id' => $landingPage->getId(),
                'status' => $landingPage->getStatus(),
                'title' => $landingPage->getTitle(),
                'subTitle' => $landingPage->getSubTitle(),
                'permalink' => $landingPage->getPermalink(),
                'pageContent' => $landingPage->getContent(),
                'metaDescription' => $landingPage->getMetaDescription(),
                'overwriteTitle' => $landingPage->getMetaTitle(),
                'selectedShop' => $shopId,
            );
            $this->view->landingPage = $landingPageInfo;

            if ($this->_request->isPost()) {
                $shopId = intval(FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('selectedShop')));
                if ($shopId <= 0) {
                    $this->view->landingPage = $this->getAllParams();
                    $this->setFlashMessage('error', 'Invalid Shop');
                } else {
                    $shop = AdminFactory::getShop()->execute($shopId);
                    $conditions = array(
                        'shop' => $shop
                    );
                    $landingPagesForShop = AdminFactory::getLandingPages()->execute($conditions);
                    if ((count($landingPagesForShop) == 1 && $landingPagesForShop[0]->getId() != $landingPageId) || count($landingPagesForShop) > 1) {
                        $this->view->landingPage = $this->getAllParams();
                        $this->setFlashMessage('error', 'Landing Page already exists for '.$shop->getName().'. Please select a different shop.');
                    } else {
                        $parameters['status'] = intval(FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('status')));
                        $parameters['title'] = FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('title'));
                        $parameters['permalink'] = FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('permalink'));
                        $parameters['subTitle'] = FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('subTitle'));
                        $parameters['metaTitle'] = FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('overwriteTitle'));
                        $parameters['metaDescription'] = FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('metaDescription'));
                        $parameters['content'] = FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('pageContent'));
                        $parameters['shop'] = $shop;

                        $result = AdminFactory::updateLandingPage()->execute($landingPage, $parameters);

                        if ($result instanceof Errors) {
                            $this->view->landingPage = $this->getAllParams();
                            $errors = $result->getErrorsAll();
                            $this->setFlashMessage('error', $errors);
                        } else {
                            self::updateVarnish($permalink);
                            $this->setFlashMessage('success', 'Landing Page has been updated successfully');
                            $this->redirect(HTTP_PATH.'admin/landingpages');
                        }
                    }
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
        self::updateVarnish($result->getPermalink());
        $this->setFlashMessage('success', 'Landing page deleted successfully.');
        die;
    }

    public function updateVarnish($permalink)
    {
        $varnishObj = new \KC\Repository\Varnish();
        $varnishObj->addUrl(HTTP_PATH_FRONTEND.'glp/'.$permalink);
    }

    public function validatepermalinkAction()
    {
        $isValid = false;
        $permalink = FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('permalink'));
        $landingPageId = intval(FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('editId')));

        $pattern = array("/&amp;/", "/[\,+@#$%'&*!;&\"<>\^()]+/", '/\s/', "/-{2,}/");
        $replaceWith = array("", "", "-", "-");
        $urlString = preg_replace($pattern, $replaceWith, trim($permalink));
        $permalink = strtolower($urlString);

        $conditions = array(
            'permalink' => $permalink
        );
        $landingPages = AdminFactory::getLandingPages()->execute($conditions);
        if (count($landingPages) == 0) {
            $isValid = true;
        } elseif (count($landingPages) == 1 && $landingPages[0]->getId() == $landingPageId) {
            $isValid = true;
        }
        echo Zend_Json::encode($isValid);
        exit;
    }
}
