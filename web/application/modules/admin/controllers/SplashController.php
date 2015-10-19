<?php
class Admin_SplashController extends Zend_Controller_Action
{
    public $flashMessenger = '';

    public function preDispatch()
    {
        $databaseConnection = \BackEnd_Helper_viewHelper::addConnection();

        if (!\Auth_StaffAdapter::hasIdentity()) {
            $pageReferer = new \Zend_Session_Namespace('referer');
            $pageReferer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }

        \BackEnd_Helper_viewHelper::closeConnection($databaseConnection);
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');
        $sessionNamespace = new \Zend_Session_Namespace();

        if ($sessionNamespace->settings['rights']['administration']['rights'] != '1'
            && $sessionNamespace->settings['rights']['administration']['rights'] !='2' ) {
            $flashMessenger = $this->_helper->getHelper('FlashMessenger');
            $message = $this->view->translate('You have no permission to access page');
            $flashMessenger->addMessage(array('error' => $message));
            $this->_redirect('/admin');
        }
        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->splashObject = new \KC\Repository\Splash();
    }

    public function indexAction()
    {
        $splashTableData = $this->splashObject->getSplashInformation();
        if (!empty($splashTableData)) {
            \BackEnd_Helper_DatabaseManager::addConnection($splashTableData[0]['locale']);
            $splashOfferDetails = $this->splashObject->getOfferById($splashTableData[0]['offerId']);
            $this->view->currentOfferTitle = $splashOfferDetails->title;
            $this->view->currentOfferLocale = $splashTableData[0]['locale'];
        }

        $this->getFlashMessage();
    }

    public function addOfferAction()
    {
        $urlRequest = $this->getRequest();
        $this->view->websites = \KC\Repository\Website::getAllWebsites();
        $this->getFlashMessage();

        if ($this->_request->isPost()) {
            $localeId = $urlRequest->getParam('locale', false);
            $locale = \BackEnd_Helper_viewHelper::getLocaleByWebsite($localeId);
            $offerId = $urlRequest->getParam('searchOfferId', false);
            $splashTableData = $this->splashObject->getSplashInformation();

            if (!empty($splashTableData)) {
                $this->splashObject->deleteSplashoffer();
            }

            $this->splashObject->saveSplashOffer($offerId, $locale);
            $varnishObject = new \KC\Repository\Varnish();
            $varnishObject->addUrl("http://www.flipit.com");
            $this->setFlashMessage('Offer has been added successfully');
            $this->_redirect(HTTP_PATH . 'admin/splash');
        }
    }

    public function shopsListAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            $localeId = intval($this->getRequest()->getParam('locale', false));
            if ($localeId) {
                $locale = \BackEnd_Helper_viewHelper::getLocaleByWebsite($localeId);
                \BackEnd_Helper_DatabaseManager::addConnection($locale);
                $searchKeyword = $this->getRequest()->getParam('keyword');
                $activeShops = $stores = \KC\Repository\Shop::getStoresForSearchByKeyword(
                    $searchKeyword,
                    25,
                    ''
                );
                $shops = array();
                if (!empty($activeShops)) {
                    foreach ($activeShops as $activeShop) {
                        $shops[] = array('name' => ucfirst($activeShop['name']),
                            'id' => $activeShop['id']);
                    }
                } else {
                    $message = $this->view->translate('No Record Found');
                    $shops[] = array('name' => $message);
                }
                $this->_helper->json($shops);
            }
        }
        exit();
    }

    public function offersListAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            $localeId = intval($this->getRequest()->getParam('locale', false));
            if ($localeId) {
                $locale = \BackEnd_Helper_viewHelper::getLocaleByWebsite($localeId);
                \BackEnd_Helper_DatabaseManager::addConnection($locale);
                $offers = new \KC\Repository\Offer();
                $offerKeyword = $this->getRequest()->getParam('keyword');
                $shopId = $this->getRequest()->getParam('shop');
                $activeCoupons = $offers->getActiveCoupons($offerKeyword, $shopId);
                $coupons = array();
                if (!empty($activeCoupons)) {
                    foreach ($activeCoupons as $activeCoupon) {
                        $coupons[] = array('name' => ucfirst($activeCoupon['title']),
                                         'id' => $activeCoupon['id']);
                    }
                } else {
                    $message = $this->view->translate('No Record Found');
                    $coupons[] = array('name' => $message);
                }
                $this->_helper->json($coupons);
            }
        }
        exit();
    }

    public function deleteOfferAction()
    {
        $this->_helper->layout->disableLayout();
        $this->getFlashMessage();
        $this->splashObject->deleteSplashoffer();
        $this->setFlashMessage('Offer has been deleted successfully');
        $this->_redirect(HTTP_PATH . 'admin/splash');
    }

    public function getFlashMessage()
    {
        $message = $this->flashMessenger->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ? $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ? $message[0]['error'] : '';
        return $this;
    }

    public function setFlashMessage($messageText)
    {
        $message = $this->view->translate($messageText);
        $this->flashMessenger->addMessage(array('success' => $message));
        return $this;
    }
}
