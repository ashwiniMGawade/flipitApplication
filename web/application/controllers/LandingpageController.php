<?php

use \Core\Domain\Factory\GuestFactory;
use \Core\Service\Errors;

class LandingpageController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout()->disableLayout();
    }

    public function indexAction()
    {
        if ($this->getRequest()->isPost()) {
            $params = $this->getAllParams();
            $visitorEmail = new Zend_Session_Namespace('emailAddressSignup');
            $visitorEmail->emailAddressSignup = $params['emailAddress'];
            $signUpUrl= HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('link_inschrijven');
            header('location:'. $signUpUrl);
        }
        $pagePermalink = $this->_helper->Error->getPageParmalink(ltrim($this->_request->getPathInfo(), '/'));
        $pagePermalink = $this->_helper->Error->getPageNumbering($pagePermalink);
        $conditions = array(
            'permalink' => $pagePermalink,
            'status' => 1
        );
        $landingPage = GuestFactory::getLandingPage()->execute($conditions);
        if ($landingPage instanceof Errors) {
            throw new \Zend_Controller_Action_Exception('', 404);
        }
        $shop = $landingPage->getShop();
        $allShopDetailKey = 'shopDetails_'.$shop->getId().'_list';
        $shopInformation = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            (string)$allShopDetailKey,
            array('function' => 'KC\Repository\Shop::getStoreDetailsForStorePage', 'parameters' => array($shop->getId())
            ),
            ''
        );

        /* We could not use this because comparing cannot be used in findBy method.. We need to recheck this.
         * $offerConditions = array(
            'shopOffers' => $shop,
            'deleted' => 0,
            'offline' => 0,
            'endDate' => ''
        );
        $offers = GuestFactory::getOffers()->execute($offerConditions);*/

        $allOffersInStoreKey = '6_topOffers'.$shop->getId().'_list';
        $offers = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            (string)$allOffersInStoreKey,
            array(
                'function' => 'KC\Repository\Offer::getAllOfferOnShop',
                'parameters' => array($shop->getId())
            ),
            ''
        );
        $offers = \FrontEnd_Helper_OffersPartialFunctions::reorderOffers($offers);

        $offersAddedInShopKey = "offersAdded_".$shop->getId()."_shop";
        $this->view->offersAddedInShop = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            (string)$offersAddedInShopKey,
            array(
                'function' => 'KC\Repository\Offer::getNumberOfOffersCreatedByShopId',
                'parameters' => array($shop->getId())
            ),
            ''
        );
        $this->view->offers                     = $offers;
        $this->view->landingPage                = $landingPage;
        $this->view->currentStoreInformation    = $shopInformation;
    }
}
