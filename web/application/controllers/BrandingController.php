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
        $this->redirect($storeUrl);
    }

    public function saveAction()
    {
        $this->redirect($this->_helper->branding->save());
    }

    public function stopAction()
    {
        $redirectUrl = $this->_helper->branding->stop();
        $this->redirect($redirectUrl);
    }

    public function startGlpAction()
    {
        $session = new Zend_Session_Namespace('BrandingGlp');
        $httpScheme = FrontEnd_Helper_viewHelper::getServerNameScheme();
        $session->saveUrl = 'http://'.$httpScheme.'.kortingscode.nl/';
        $redirectUrl = $this->_helper->branding->startGLP();
        $this->redirect($redirectUrl);
    }

    public function saveGlpAction()
    {
        $this->redirect($this->_helper->branding->saveGLP());
    }

    public function stopGlpAction()
    {
        $redirectUrl = $this->_helper->branding->stopGLP();
        $this->redirect($redirectUrl);
    }
}
