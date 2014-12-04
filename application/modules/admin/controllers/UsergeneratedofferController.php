<?php
class Admin_UsergeneratedofferController extends Zend_Controller_Action
{
    public function preDispatch()
    {
        $dbConnection = BackEnd_Helper_viewHelper::addConnection();//connection generate with second database
        $params = $this->_getAllParams();
        if (!Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }
        BackEnd_Helper_viewHelper::closeConnection($dbConnection);
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');
        $sessionNamespace = new Zend_Session_Namespace();
        if ($sessionNamespace->settings['rights']['system manager']['rights'] != '1') {
            $this->_redirect('/admin/auth/index');
        }
    }

    public function getofferAction()
    {
        $params = $this->_getAllParams();
        $offerList = UserGeneratedOffer::getOfferList($params);
        echo Zend_Json::encode($offerList);
        die();
    }

    public function indexAction()
    {
        $flashMessage = $this->_helper->getHelper('FlashMessenger');
        $message = $flashMessage->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ? $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ? $message[0]['error'] : '';
    }

    public function searchtopfiveshopAction()
    {
        $seachString = $this->getRequest()->getParam('keyword');
        $deletedFlag = $this->getRequest()->getParam('flag');
        $searchResults = UserGeneratedOffer::searchToFiveShop($seachString, $deletedFlag);
        $resultWithoutDuplication = array();
        $removeDuplicateRecords = array();
        if (sizeof($searchResults) > 0) {
            foreach ($searchResults as $d) {
                $id =  $d['shop']['id'];
                if (isset($removeDuplicateRecords[$id])) {
                    $removeDuplicateRecords[$id] = $id;
                } else {
                    $removeDuplicateRecords[$id] = $id;
                    $resultWithoutDuplication[] = ucfirst($d['name']);
                }
            }
        } else {
            $recordNotFoundMessage = $this->view->translate('No Record Found');
            $resultWithoutDuplication[] = $recordNotFoundMessage;
        }
        echo Zend_Json::encode($resultWithoutDuplication);
        die;
    }

    public function searchtopfiveofferAction()
    {
        $seachString = $this->getRequest()->getParam('keyword');
        $deletedFlag = $this->getRequest()->getParam('flag');
        $searchResults = UserGeneratedOffer::searchToFiveOffer($seachString, $deletedFlag);
        $resultWithoutDuplication = array();
        $removeDuplicateRecords = array();
        if (sizeof($searchResults) > 0) {
            foreach ($searchResults as $d) {
                $id =  $d['id'];
                if (isset($removeDuplicateRecords[$id])) {
                    $removeDuplicateRecords[$id] = $id;
                } else {
                    $removeDuplicateRecords[$id] = $id;
                    $resultWithoutDuplication[] = ucfirst($d['title']);
                }
            }
        } else {
            $recordNotFoundMessage = $this->view->translate('No Record Found');
            $resultWithoutDuplication[] = $recordNotFoundMessage;
        }
        echo Zend_Json::encode($resultWithoutDuplication);
        die;
    }

    public function searchtopfivecouponAction()
    {
        $seachString = $this->getRequest()->getParam('keyword');
        $deletedFlag = $this->getRequest()->getParam('flag');
        $searchResults = UserGeneratedOffer::searchToFiveCoupon($seachString, $deletedFlag);
        $resultWithoutDuplication = array();
        if (sizeof($searchResults) > 0) {
            foreach ($searchResults as $d) {
                $id =  $d['id'];
                $resultWithoutDuplication[] = $d['couponCode'];
            }
        } else {
            $recordNotFoundMessage = $this->view->translate('No Record Found');
            $resultWithoutDuplication[] = $recordNotFoundMessage;
        }
        echo Zend_Json::encode($resultWithoutDuplication);
        die;
    }

    public function permanentdeleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        $deletePermanent = Offer::deleteOffer($id);
        die;
    }

    public function editofferAction()
    {
        $params = $this->_getAllParams();
        $this->view->offerId = $params['id'];
        $this->view->qstring = $_SERVER['QUERY_STRING'];
        $shop = Offer::getOfferShopDetail($params['id']);
        $this->view->offerShoLogo = $shop;
        $this->view->shopList = Shop::getOfferShopList();
        $this->view->catList= Category::getCategoriesInformation();
        $pageObj = new Page();
        $this->view->pages = $pageObj->getPagesOffer();
        $allTiles = OfferTiles::getAllTiles();
        $this->view->tiles = $allTiles;
    }

    public function updateofferAction()
    {
        $params = $this->_getAllParams();
        $offer = Doctrine_Core::getTable("Offer")->find($params['offerId']);
        if (!empty($params['approveSocialCode'])) {
            $offer->approved = 1;
        } else {
            $offer->approved = 0;
        }
        $offer->save();

        $offerUpdate = $offer->updateOffer($params);
        $flashMessage = $this->_helper->getHelper('FlashMessenger');
        if ($offerUpdate['result']) {
            self::updateVarnish($params['offerId']);
            $message = $this->view->translate('Offer has been updated successfully.');
            $flashMessage->addMessage(array('success' => $message ));
        } else {
            $message = $this->view->translate('Error: Your file size exceeded 2MB');
            $flashMessage->addMessage(array('error' => $message ));
        }
        $this->_redirect(HTTP_PATH.'admin/usergeneratedoffer#'.$params['qString']);
        die;
    }

    public function updateVarnish($id)
    {
        $varnishObj = new Varnish();
        $varnishObj->addUrl(HTTP_PATH_FRONTEND);
        $varnishObj->addUrl(HTTP_PATH_FRONTEND . FrontEnd_Helper_viewHelper::__link('link_nieuw'));
        $varnishObj->addUrl(HTTP_PATH_FRONTEND . FrontEnd_Helper_viewHelper::__link('link_top-20'));
        $varnishObj->addUrl(HTTP_PATH_FRONTEND . FrontEnd_Helper_viewHelper::__link('link_categorieen'));
        $varnishObj->addUrl("http://www.flipit.com");
        if (LOCALE == '') {
            $varnishObj->addUrl(HTTP_PATH_FRONTEND  . 'marktplaatsfeed');
            $varnishObj->addUrl(HTTP_PATH_FRONTEND . 'marktplaatsmobilefeed');
        }
        $varnishUrls = Offer::getAllUrls($id);
        if (isset($varnishUrls) && count($varnishUrls) > 0) {
            foreach ($varnishUrls as $value) {
                $varnishObj->addUrl(HTTP_PATH_FRONTEND . $value);
            }
        }
    }

    public function shopdetailAction()
    {
        $params = $this->_getAllParams();
        echo Zend_Json::encode(Shop::getShopDetail($params['shopId']));
        die;
    }
}
