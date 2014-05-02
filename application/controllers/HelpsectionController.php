<?php

class HelpsectionController extends Zend_Controller_Action
{

    /**
     * override views based on modules if exists
     * @see Zend_Controller_Action::init()
     * @author Bhart
     */
    public function init()
    {
        $module   = strtolower($this->getRequest()->getParam('lang'));
        $controller = strtolower($this->getRequest()->getControllerName());
        $action     = strtolower($this->getRequest()->getActionName());

        # check module specific view exists or not
        if (file_exists (APPLICATION_PATH . '/modules/'  . $module . '/views/scripts/' . $controller . '/' . $action . ".phtml")){

            # set module specific view script path
            $this->view->setScriptPath( APPLICATION_PATH . '/modules/'  . $module . '/views/scripts' );
        } else{

            # set default module view script path
            $this->view->setScriptPath( APPLICATION_PATH . '/views/scripts' );
        }
    }

    public function indexAction()
    {
        $id = 13;
        $pages = Page::getPageList($id);
        $this->view->pageList = $pages;
        $params = $this->_getAllParams();
        if($params['id']){
            $dataList = Page :: getDefaultPage($params['id']);
            $this->view->data = $dataList;
            //echo "<pre>";
            //print_r($dataList);die;
            $this->view->id = $params['id'];
        } else{

        }

    }
/*public function getpagesAction() {
    $id = intval($this->getRequest()->getParam('id'));
    if(intval($id) > 0 ){
        $data = Page :: getFAQ(5);
        //	print_r($data);die;
        //$this->view->data = $data ;
        $this->view->id = $id;
        //$this->view->userDetail = $data;
        //print_r($data);die;
        //$this->view->favShopId='';
        //print_r($data);die;
    }
    die();
}*/

}
