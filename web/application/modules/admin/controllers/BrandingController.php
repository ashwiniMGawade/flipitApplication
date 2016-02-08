<?php
class Admin_BrandingController extends Application_Admin_BaseController
{
    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function startAction()
    {
        $session = new Zend_Session_Namespace('Branding');
        $refererUrl = \FrontEnd_Helper_viewHelper::getRefererHostUrl();
        $session->saveUrl = 'http://'.$refererUrl.'/admin/';
        $storeUrl = $this->_helper->branding->start();
        $this->redirect( $storeUrl );
    }

    public function saveAction()
    {
        $this->redirect($this->_helper->branding->save());
    }

    public function stopAction()
    {
        $redirectUrl = $this->_helper->branding->stop();
        $this->_redirect( $redirectUrl );
    }

    public function startGlpAction()
    {
        $session = new Zend_Session_Namespace('BrandingGlp');
        $refererUrl = \FrontEnd_Helper_viewHelper::getRefererHostUrl();
        $session->saveUrl = 'http://'.$refererUrl.'/admin/';
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
