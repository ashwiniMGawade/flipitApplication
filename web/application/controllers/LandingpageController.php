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
        $pagePermalink = $this->_helper->Error->getPageParmalink(ltrim($this->_request->getPathInfo(), '/'));
        $pagePermalink = $this->_helper->Error->getPageNumbering($pagePermalink);
        $landingPage = GuestFactory::getLandingPage()->execute(array('permalink' => $pagePermalink));
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

        $allOffersInStoreKey = '6_topOffers'.$shop->getId().'_list';
        $offers = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            (string)$allOffersInStoreKey,
            array(
                'function' => 'KC\Repository\Offer::getAllOfferOnShop',
                'parameters' => array($shop->getId())
            ),
            ''
        );

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