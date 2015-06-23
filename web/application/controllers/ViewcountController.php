<?php
class ViewcountController extends Zend_Controller_Action
{
    public function storecountAction()
    {
        $type = $this->_getParam('type');
        $clickEvent = $this->_getParam('event');
        $id = $this->_getParam('id');
        $viewCountValue  = \FrontEnd_Helper_viewHelper::viewCounter($type, $clickEvent, $id);
        $viewCountValue = $viewCountValue == "false" ? false : true;
        echo \Zend_Json::encode($viewCountValue);
        exit();
    }
}
