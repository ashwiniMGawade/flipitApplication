<?php
class ConversiondataController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $conversionId = $this->getRequest()->getParam("id", false);
        $conversionInfo = \KC\Repository\Conversions::getConversionInformationById($conversionId);
        echo $this->_helper->json->sendJson($conversionInfo);
    }
}
