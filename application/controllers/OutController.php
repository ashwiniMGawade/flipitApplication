<?php
class OutController extends Zend_Controller_Action
{
    public function offerAction()
    {
        $offerId = $this->getRequest()->getParam('id');
        \FrontEnd_Helper_viewHelper::viewCounter('offer', 'onclick', $offerId);
        \FrontEnd_Helper_viewHelper::viewCounter('offer', 'onload', $offerId);
        \KC\Repository\Offer::addConversion($offerId);
        $redirectUrl  = \KC\Repository\Offer::getCloakLink($offerId, false);
        $this->_helper->redirector->setCode(301);
        $this->_redirect($redirectUrl);
    }

    public function exofferAction()
    {
        $offerId = $this->getRequest()->getParam('id');
        $redirectUrl  = \KC\Repository\Offer::getCloakLink($offerId, false);
        $this->_helper->redirector->setCode(301);
        $this->_redirect($redirectUrl);
    }

    public function shopAction()
    {
        $shopId = $this->getRequest()->getParam('id');
        \FrontEnd_Helper_viewHelper::viewCounter('shop', 'onclick', $shopId);
        \KC\Repository\Shop::addConversion($shopId);
        $redirectUrl = \KC\Repository\Shop::getStoreLinks($shopId, false);
        $this->_helper->redirector->setCode(301);
        $this->_redirect($redirectUrl);
    }
}
