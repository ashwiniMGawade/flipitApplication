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
        $this->_redirect( $storeUrl );
    }

    public function saveAction()
    {
        $this->_redirect($this->_helper->branding->save());
    }

    public function stopAction()
    {
        $redirectUrl = $this->_helper->branding->stop();
        $this->_redirect( $redirectUrl );
    }
}
