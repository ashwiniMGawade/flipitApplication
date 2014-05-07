<?php
class Admin_BrandingController extends Zend_Controller_Action {

    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function startAction()
    {
        $session = new Zend_Session_Namespace('Branding');
        $session->saveUrl = 'http://www.flipit.com/admin/';
        $storeUrl = $this->_helper->branding->start();
        $this->_redirect( $storeUrl );
    }

    public function saveAction()
    {
        $this->_helper->branding->save();
        $this->_redirect( $_SERVER['HTTP_REFERER'] );
    }

    public function stopAction()
    {
        $this->_redirect( 'http://www.flipit.com/admin' );
    }
}
?>