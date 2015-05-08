<?php

class WhitelabelController extends  Zend_Controller_Action
{

    public function init()
    {
    /* Initialize action controller here */
    //  $this->_helper->layout()->disableLayout();
    }

    public function top10XmlAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        \FrontEnd_Helper_viewHelper::top10Xml(true);
        $this->_response->setHeader('Content-Type', 'text/xml; charset=utf-8');
    }

}
