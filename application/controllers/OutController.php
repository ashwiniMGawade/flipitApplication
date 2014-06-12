<?php

class OutController extends Zend_Controller_Action
{

    public function offerAction()
    {
       $offerId = $this->getRequest()->getParam('id');
       FrontEnd_Helper_viewHelper::viewCounter('offer', 'onclick', $offerId);
       FrontEnd_Helper_viewHelper::viewCounter('offer', 'onload', $offerId);
       Offer::addConversion($offerId);
       $link  = Offer::getCloakLink($offerId , false );
       $this->_helper->redirector->setCode(301);
       $this->_redirect($link);
    }

    /**
     * Get offer links for cloaking purpose
     *
     * @author Raman
     * @version 1.0
     */
    public function exofferAction()
    {
        $offer_id = $this->getRequest()->getParam('id');
        $link  = Offer::getCloakLink($offer_id , false );
        $this->_helper->redirector->setCode(301);
        $this->_redirect($link);
    }

    /**
     * Get shop links for cloaking purpose
     *
     * @author Raman
     * @version 1.0
     */
    public function shopAction()
    {
        $shop_id = $this->getRequest()->getParam('id');
        //view count for Shop
        FrontEnd_Helper_viewHelper::viewCounter('shop', 'onclick', $shop_id);
        //FrontEnd_Helper_viewHelper::viewCounter('shop', 'onload', $offerId);
        Shop::addConversion($shop_id);
        $link = Shop::getStoreLinks($shop_id , false );
        $this->_helper->redirector->setCode(301);
        $this->_redirect($link);
    }

    public function clearcacheAction()
    {
        $cache = Zend_Registry::get('cache');
        $cache->clean();
        echo 'cache is cleared';
    }




}
