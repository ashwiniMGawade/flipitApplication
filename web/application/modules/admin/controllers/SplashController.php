<?php

use \Core\Domain\Factory\SystemFactory;
use \Core\Domain\Factory\AdminFactory;
use \Core\Service\Errors;

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
        if (false == empty($splashOffers)) {
            foreach ($splashOffers as $splashOffer) {
                $offer = SystemFactory::getOffer($splashOffer->getLocale())->execute(array( 'id' => $splashOffer->getOfferId()));
                if ($offer instanceof  \Core\Domain\Entity\Offer) {
                    $splashOffersData[] = array(
                        'id' => $splashOffer->getId(),
                        'locale' => $splashOffer->getLocale(),
                        'offer' => $offer->getTitle(),
                        'shop'  => $offer->getShopOffers()->getName(),
                    );
                }
            }
        }
        $this->view->splashOffersData = $splashOffersData;
    }

    public function pageAction()
    {
        $splashPage = SystemFactory::getSplashPage()->execute(array('id' => 1));
        $this->view->splashPage = $splashPage;
        if ($this->_request->isPost()) {
            $pageParams = $this->getRequest()->getParam('splashPage', false);
            if (true === isset($_FILES['splashImage']) && true === isset($_FILES['splashImage']['name']) && '' !== $_FILES['splashImage']['name']) {
                $rootPath = BASE_PATH . 'images/upload/splash/';
                $image = $this->uploadImage('splashImage', $rootPath);
                $pageParams['image'] = $image;
                $oldFile = $rootPath . $splashPage->getImage();
                if ($image !== false && $image !== $splashPage->getImage() && true === file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
            if (true === isset($_FILES['splashInfoImage']) && true === isset($_FILES['splashInfoImage']['name']) && '' !== $_FILES['splashInfoImage']['name']) {
                $rootPath = BASE_PATH . 'images/upload/splash/';
                $infoImage = $this->uploadImage('splashInfoImage', $rootPath);
                $pageParams['infoImage'] = $infoImage;
                $oldFile = $rootPath . $splashPage->getInfoImage();
                if ($infoImage !== false && $infoImage !== $splashPage->getInfoImage() && true === file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
            $result = AdminFactory::updateSplashPage()->execute($splashPage, $pageParams);
            if ($result instanceof Errors) {
                $errors = $result->getErrorsAll();
                $this->setFlashMessage('error', $errors);
            } else {
                $this->refreshSplashPageVarnish();
                $this->setFlashMessage('success', 'Splash page has been updated successfully');
            }
        }
    }

    public function addOfferAction()
    {
        $urlRequest = $this->getRequest();
        $this->view->websites = \KC\Repository\Website::getAllWebsites();
        if ($this->_request->isPost()) {
            $localeId = $urlRequest->getParam('locale', false);
            if (true == empty($localeId)) {
                $this->setFlashMessage('error', 'Locale should not be blank.');
                $this->redirect(HTTP_PATH . 'admin/splash');
            }
            $locale = \BackEnd_Helper_viewHelper::getLocaleByWebsite($localeId);
            $params = array(
                'locale' => $locale,
                'shopId' => $urlRequest->getParam('searchShopId', false),
                'offerId' => $urlRequest->getParam('searchOfferId', false)
            );
            $splashOffers = SystemFactory::getSplashOffers()->execute($params);

            if (count($splashOffers)>0) {
                $this->setFlashMessage('error', 'This splash offer already exist.');
                $this->redirect(HTTP_PATH . 'admin/splash');
            }
            $splashOffers = SystemFactory::getSplashOffers()->execute();
            $position = count($splashOffers) + 1;

            $params['position'] = $position;
            $splashOffer = AdminFactory::createSplashOffer()->execute();
            $result = AdminFactory::addSplashOffer()->execute($splashOffer, $params);
            if ($result instanceof Errors) {
                $errors = $result->getErrorsAll();
                $this->setFlashMessage('error', $errors);
            } else {
                $this->refreshSplashPageVarnish();
                $this->setFlashMessage('success', 'Offer has been added successfully');
            }
            $this->redirect(HTTP_PATH . 'admin/splash');
        }
    }

    public function reorderAction()
    {
        $params = $this->getRequest()->getParams();
        $splashOffers = ( array ) SystemFactory::getSplashOffers()->execute(array());
        $splashOffers = $this->rekeyObjects($splashOffers, 'Id');
        foreach ($params['splashOffers'] as $order => $splashOfferId) {
            if (true == array_key_exists($splashOfferId, $splashOffers)) {
                $splashOffer = $splashOffers[$splashOfferId];
                $params = array( 'position' => $order+1);
                $result = AdminFactory::updateSplashOffer()->execute($splashOffer, $params);
            }
        }
        $this->refreshSplashPageVarnish();
        $this->setFlashMessage('success', 'Offers has been reordered successfully');
        $this->redirect(HTTP_PATH . 'admin/splash');
    }

    public function deleteOfferAction()
    {
        $splashOfferId = intval($this->getRequest()->getParam('id'));
        if (intval($splashOfferId) < 1) {
            $this->setFlashMessage('error', 'Invalid selection.');
            $this->redirect(HTTP_PATH . 'admin/splash');
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

    public function imagesAction()
    {
        $splashImages = ( array ) SystemFactory::getSplashImages()->execute(array(), array('position'=>'ASC'));
        if ($this->_request->isPost()) {
            $splashImage = AdminFactory::createSplashImage()->execute();
            $rootPath = BASE_PATH . 'images/upload/splash/';
            $uploadedImage = '';
            if (true === isset($_FILES['featuredImage']) && true === isset($_FILES['featuredImage']['name']) && '' !== $_FILES['featuredImage']['name']) {
                $uploadedImage = $this->uploadImage('featuredImage', $rootPath);
            }
            $params['image'] = $uploadedImage;
            $params['position'] = count($splashImages) + 1;
            $result = AdminFactory::addSplashImage()->execute($splashImage, $params);
            if ($result instanceof Errors) {
                $errors = $result->getErrorsAll();
                $this->setFlashMessage('error', $errors);
            } else {
                $this->refreshSplashPageVarnish();
                $this->setFlashMessage('success', 'Featured image has been added successfully');
                $splashImages = ( array ) SystemFactory::getSplashImages()->execute(array(), array('position'=>'ASC'));
            }
        }
        $this->view->splashImages = $splashImages;
    }

    public function addFeaturedImageAction()
    {
        $this ->_helper-> layout()->disableLayout();
    }

    public function reorderImagesAction()
    {
        $params = $this->getRequest()->getParams();
        $splashImages = ( array ) SystemFactory::getSplashImages()->execute(array(), array('position'=>'ASC'));
        $splashImages = $this->rekeyObjects($splashImages, 'Id');
        foreach ($params['splashImages'] as $order => $splashImageId) {
            if (true == array_key_exists($splashImageId, $splashImages)) {
                $splashImage = $splashImages[$splashImageId];
                $params = array( 'position' => $order+1);
                AdminFactory::updateSplashImage()->execute($splashImage, $params);
            }
        }
        $this->refreshSplashPageVarnish();
        $this->setFlashMessage('success', 'Featured images has been reordered successfully');
        $this->redirect(HTTP_PATH . 'admin/splash/images');
    }

    public function deleteImageAction()
    {
        $splashImageId = intval($this->getRequest()->getParam('id'));
        if (intval($splashImageId) < 1) {
            $this->setFlashMessage('error', 'Invalid selection.');
            $this->redirect(HTTP_PATH . 'admin/splash/images');
        }

        $splashImages = ( array ) SystemFactory::getSplashImages()->execute(array());
        $splashOffers = ( array ) SystemFactory::getSplashOffers()->execute(array());
        if (count($splashOffers) >= count($splashImages)) {
            $this->setFlashMessage('error', 'You can\'t delete this image as it is associated with an offer.');
            $this->redirect(HTTP_PATH . 'admin/splash/images');
        }

        $result = AdminFactory::getSplashImage()->execute(array('id' => $splashImageId));
        if ($result instanceof Errors) {
            $errors = $result->getErrorsAll();
            $this->setFlashMessage('error', $errors);
            $this->redirect(HTTP_PATH . 'admin/splash/images');
        }
        AdminFactory::deleteSplashImages()->execute($result);
        $image = BASE_PATH . 'images/upload/splash/' . $result->getImage();
        if (true === file_exists($image)) {
            unlink($image);
        }
        $this->refreshSplashPageVarnish();
        $this->setFlashMessage('success', 'Splash image deleted successfully.');
        $this->redirect(HTTP_PATH . 'admin/splash/images');
    }

    public function uploadImage($file, $rootPath)
    {
        $adapter = new \Zend_File_Transfer_Adapter_Http();
        $adapter->getFileInfo($file);
        if (!file_exists($rootPath)) {
            mkdir($rootPath, 0755, true);
        } elseif(!is_writable($rootPath)) {
            chmod($rootPath, 0755);
        }

        $adapter->setDestination($rootPath);
        $adapter->addValidator('Extension', false, array('jpg,jpeg,png', true));
        $imageName = $adapter->getFileName($file, false);
        $targetPath = $rootPath . $imageName;
        $adapter->addFilter(
            new \Zend_Filter_File_Rename(
                array('target' => $targetPath, 'overwrite' => true)
            ),
            null,
            $file
        );
        $adapter->receive($file);
        if ($adapter->isValid($file)) {
            return $imageName;
        } else {
            return false;
        }
    }

    public function refreshSplashPageVarnish()
    {
        $varnishObject = new \KC\Repository\Varnish();
        $varnishObject->addUrl("http://www.flipit.com");
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
}
