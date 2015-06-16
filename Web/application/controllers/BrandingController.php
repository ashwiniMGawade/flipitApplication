<?php
class BrandingController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function startAction()
    {
        $session = new Zend_Session_Namespace('Branding');
        $httpScheme = FrontEnd_Helper_viewHelper::getServerNameScheme();
        $session->saveUrl = 'http://'.$httpScheme.'.kortingscode.nl/';
        $storeUrl = $this->_helper->branding->start();
        $this->_redirect($storeUrl);
    }

    public function saveAction()
    {
        $this->_redirect($this->_helper->branding->save());
    }

    public function stopAction()
    {
        $redirectUrl = $this->_helper->branding->stop();
        $this->_redirect($redirectUrl);
    }
}
