<?php

use \Core\Domain\Factory\AdminFactory;
class OutController extends Zend_Controller_Action
{
    public function offerAction()
    {
        $offerId = $this->getRequest()->getParam('id');
        FrontEnd_Helper_viewHelper::viewCounter('offer', 'onclick', $offerId);
        $conversionId = \KC\Repository\Conversions::addConversion($offerId, 'offer');
        $clickout = new FrontEnd_Helper_ClickoutFunctions($offerId, null);
        $redirectUrl = $clickout->getCloakLink('offer', $conversionId);
        $this->_helper->redirector->setCode(301);
        $this->_redirect($redirectUrl);
    }

    public function glpAction()
    {
        $landingPageRefUrl = null;
        $landingPageId = $this->getRequest()->getParam('landingPageId');
        $conditions['id'] = $landingPageId;
        $landingPage = AdminFactory::getLandingPage()->execute($conditions);
        if($landingPage instanceof \Core\Domain\Entity\LandingPage) {
            $landingPageRefUrl = $landingPage->getRefUrl();
        }
        $offerId = $this->getRequest()->getParam('offerId');
        FrontEnd_Helper_viewHelper::viewCounter('offer', 'onclick', $offerId);
        $conversionId = \KC\Repository\Conversions::addConversion($offerId, 'offer');
        $clickout = new FrontEnd_Helper_ClickoutFunctions($offerId, null, $landingPageRefUrl);
        $redirectUrl = $clickout->getCloakLink('offer', $conversionId);
        $this->_helper->redirector->setCode(301);
        $this->_redirect($redirectUrl);
    }

    public function exofferAction()
    {
        $offerId = $this->getRequest()->getParam('id');
        $clickout = new FrontEnd_Helper_ClickoutFunctions($offerId, null);
        $redirectUrl = $clickout->getCloakLink('offer');
        $this->_helper->redirector->setCode(301);
        $this->_redirect($redirectUrl);
    }

    public function shopAction()
    {
        $shopId = $this->getRequest()->getParam('id');
        FrontEnd_Helper_viewHelper::viewCounter('shop', 'onclick', $shopId);
        $conversionId = \KC\Repository\Conversions::addConversion($shopId, 'shop');
        $clickout = new FrontEnd_Helper_ClickoutFunctions(null, $shopId);
        $redirectUrl = $clickout->getCloakLink('shop', $conversionId);
        $this->_helper->redirector->setCode(301);
        $this->_redirect($redirectUrl);
    }
}
