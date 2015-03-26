<?php
class OutController extends Zend_Controller_Action
{
    public function offerAction()
    {
        $offerId = $this->getRequest()->getParam('id');
        FrontEnd_Helper_viewHelper::viewCounter('offer', 'onclick', $offerId);
        FrontEnd_Helper_viewHelper::viewCounter('offer', 'onload', $offerId);
        Offer::addConversion($offerId);
        $redirectUrl  = FrontEnd_Helper_ClickoutFunctions::getCloakLink($offerId, false);
        $this->_helper->redirector->setCode(301);
        $this->_redirect($redirectUrl);
    }

    public function exofferAction()
    {
        $offerId = $this->getRequest()->getParam('id');
        $redirectUrl  = FrontEnd_Helper_ClickoutFunctions::getCloakLink($offerId, false);
        $this->_helper->redirector->setCode(301);
        $this->_redirect($redirectUrl);
    }

    public function shopAction()
    {
        $shopId = $this->getRequest()->getParam('id');
        FrontEnd_Helper_viewHelper::viewCounter('shop', 'onclick', $shopId);
        Shop::addConversion($shopId);
        $redirectUrl = FrontEnd_Helper_ClickoutFunctions::getStoreLinks($shopId, false);
        $this->_helper->redirector->setCode(301);
        $this->_redirect($redirectUrl);
    }
}
