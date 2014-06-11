<?php
class OutController extends Zend_Controller_Action
{
    public function offerAction()
    {
        $offer_id = $this->getRequest()->getParam('id');
        FrontEnd_Helper_viewHelper::viewCounter('offer', 'onclick', $offer_id);
        FrontEnd_Helper_viewHelper::viewCounter('offer', 'onload', $offer_id);
        Offer::addConversion($offer_id);
        $link  = Offer::getcloakLink($offer_id, false);
        $this->_helper->redirector->setCode(301);
        $this->_redirect($link);
    }

    public function extendedofferAction()
    {
        $offer_id = $this->getRequest()->getParam('id');
        $link  = Offer::getcloakLink($offer_id, false);
        $this->_helper->redirector->setCode(301);
        $this->_redirect($link);
    }

    public function shopAction()
    {
        $shop_id = $this->getRequest()->getParam('id');
        FrontEnd_Helper_viewHelper::viewCounter('shop', 'onclick', $shop_id);
        Shop::addConversion($shop_id);
        $link = Shop::getStoreLinks($shop_id, false);
        $this->_helper->redirector->setCode(301);
        $this->_redirect($link);
    }
}
