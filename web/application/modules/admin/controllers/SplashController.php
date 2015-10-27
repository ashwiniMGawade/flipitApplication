<?php

use \Core\Domain\Factory\SystemFactory;
use \Core\Domain\Factory\AdminFactory;

class Admin_SplashController extends Application_Admin_BaseController
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
        $splashOffersData = array();
        $splashOffers = ( array ) SystemFactory::getSplashOffers()->execute(array(), array('position'=>'ASC'));
        if( false == empty( $splashOffers) ) {
            foreach( $splashOffers as $splashOffer ) {
                $offer = SystemFactory::getOffer($splashOffer->getLocale())->execute(array( 'id' => $splashOffer->getOfferId()));
                if( $offer instanceof  \Core\Domain\Entity\Offer) {
                    $splashOffersData[] = array(
                        'id' => $splashOffer->getId(),
                        'locale' => $splashOffer->getLocale(),
                        'offer' => $offer->getTitle(),
                        'shop'  => $offer->getShopOffers()->getTitle(),
                    );
                }
            }
        }
        $this->view->splashOffersData = $splashOffersData;
    }

    public function addOfferAction()
    {
        $urlRequest = $this->getRequest();
        $this->view->websites = \KC\Repository\Website::getAllWebsites();
        if ($this->_request->isPost()) {
            $localeId = $urlRequest->getParam('locale', false);
            $locale = \BackEnd_Helper_viewHelper::getLocaleByWebsite($localeId);
            $params = array(
                'locale' => $locale,
                'shopId' => (int) $urlRequest->getParam('searchShopId', false),
                'offerId' => (int) $urlRequest->getParam('searchOfferId', false)
            );

            $splashOffers = SystemFactory::getSplashOffers()->execute($params);

            if(count($splashOffers)>0) {
                $this->setFlashMessage('error', 'This splash offer already exist.');
                $this->redirect(HTTP_PATH . 'admin/splash');
            }
            $splashOffers = SystemFactory::getSplashOffers()->execute();
            $position = count($splashOffers) + 1;

            $params['position'] = $position;
            $splashOffer = AdminFactory::createSplashOffer()->execute();
            $result = AdminFactory::addSplashOffer()->execute($splashOffer, $params);
            if ($result instanceof Errors) {
                $this->view->widget = $this->getAllParams();
                $errors = $result->getErrorsAll();
                $this->setFlashMessage('error', $errors);
            } else {
                $this->refreshSplashPageVarnish();
                $this->setFlashMessage('success', 'Offer has been added successfully');
            }
            $this->redirect(HTTP_PATH . 'admin/splash');
        }
    }

    public function refreshSplashPageVarnish() {
        $varnishObject = new \KC\Repository\Varnish();
        $varnishObject->addUrl("http://www.flipit.com");
    }

    public function reorderAction()
    {
        $params = $this->getRequest()->getParams();
        $splashOffers = ( array ) SystemFactory::getSplashOffers()->execute(array());
        $splashOffers = $this->rekeyObjects($splashOffers, 'Id');
        foreach( $params['splashOffers'] as $order => $splashOfferId ) {
            if(true == array_key_exists($splashOfferId, $splashOffers)) {
                $splashOffer = $splashOffers[$splashOfferId];
                $params = array( 'position' => $order+1);
                $result = AdminFactory::updateSplashOffer()->execute($splashOffer, $params);
            }
        }
        $this->refreshSplashPageVarnish();
        $this->setFlashMessage('success', 'Offers has been reordered successfully');
        $this->redirect(HTTP_PATH . 'admin/splash');
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
        $splashOfferId = intval($this->getRequest()->getParam('id'));
        if (intval($splashOfferId) < 1) {
            $this->setFlashMessage('error', 'Invalid selection.');
            die;
        }

        $result = AdminFactory::getSplashOffer()->execute(array('id' => $splashOfferId));
        if ($result instanceof Errors) {
            $errors = $result->getErrorsAll();
            $this->setFlashMessage('error', $errors);
            $this->redirect(HTTP_PATH . 'admin/splash');
        }
        AdminFactory::deleteSplashOffer()->execute($result);
        $this->refreshSplashPageVarnish();
        $this->setFlashMessage('success', 'Splash Offer deleted successfully.');
        $this->redirect(HTTP_PATH . 'admin/splash');
    }
}
