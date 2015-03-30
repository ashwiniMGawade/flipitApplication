<?php
class OutController extends Zend_Controller_Action
{
    public function offerAction()
    {
        $offerId = $this->getRequest()->getParam('id');
        FrontEnd_Helper_viewHelper::viewCounter('offer', 'onclick', $offerId);
        Offer::addConversion($offerId);
        $offer = new FrontEnd_Helper_ClickoutFunctions($offerId, null);
        $redirectUrl = $offer->getCloakLink('offer');
        $this->_helper->redirector->setCode(301);
        $this->_redirect($redirectUrl);
    }

    public function exofferAction()
    {
        $offerId = $this->getRequest()->getParam('id');
        $offer = new FrontEnd_Helper_ClickoutFunctions($offerId, null);
        $redirectUrl = $offer->getCloakLink('offer');
        $this->_helper->redirector->setCode(301);
        $this->_redirect($redirectUrl);
    }

    public function shopAction()
    {
        $shopId = $this->getRequest()->getParam('id');
        FrontEnd_Helper_viewHelper::viewCounter('shop', 'onclick', $shopId);
        Shop::addConversion($shopId);
        $shop = new FrontEnd_Helper_ClickoutFunctions(null, $shopId);
        $redirectUrl = $shop->getCloakLink('shop');
        $this->_helper->redirector->setCode(301);
        $this->_redirect($redirectUrl);
    }
}
