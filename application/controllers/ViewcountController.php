<?php
class ViewcountController extends Zend_Controller_Action
{

    public function init()
    {
        $module   = strtolower($this->getRequest()->getParam('lang'));
        $controller = strtolower($this->getRequest()->getControllerName());
        $action     = strtolower($this->getRequest()->getActionName());
        if (
            file_exists(
                APPLICATION_PATH . '/modules/' . $module . '/views/scripts/' . $controller . '/' . $action . ".phtml"
            )
        ) {
            $this->view->setScriptPath(APPLICATION_PATH . '/modules/' . $module . '/views/scripts');
        } else {
            $this->view->setScriptPath(APPLICATION_PATH . '/views/scripts');
        }
    }

    public function storecountAction()
    {
        $type = $this->_getParam('type');
        $clickEvent = $this->_getParam('event');
        $id = $this->_getParam('id');
        $viewCountValue  = FrontEnd_Helper_viewHelper::viewCounter($type, $clickEvent, $id);
        $viewCountValue = $viewCountValue == "false" ? false : true;
        echo Zend_Json::encode($viewCountValue);
        exit();
    }
}
