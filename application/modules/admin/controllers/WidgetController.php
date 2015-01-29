<?php
class Admin_WidgetController extends Zend_Controller_Action
{
    /**
     * initialize flash messages on view.
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ?
        $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ?
        $message[0]['error'] : '';
    }

    /**
     * preDispatch function redirect the user to login page if session is not set.
     * @see Zend_Controller_Action::preDispatch()
     * @author mkaur
     */
    public function preDispatch()
    {
        $conn2 = \BackEnd_Helper_viewHelper::addConnection();//connection generate with second database
        $params = $this->_getAllParams();
        if (!\Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }
        \BackEnd_Helper_viewHelper::closeConnection($conn2);
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');


        # redirect of a user don't have any permission for this controller
        $sessionNamespace = new Zend_Session_Namespace();
        if($sessionNamespace->settings['rights']['content']['rights'] != '1') {
            $this->_redirect('/admin/auth/index');
        }

    }
    public function indexAction()
    {
    }

    /**
     * addwidget function save or creates widged in database through model class
     * @author mkaur
     */
    public function addwidgetAction()
    {
    if ($this->_request->isPost()) {
        $params = $this->_getAllParams();
        $flash = $this->_helper->getHelper('FlashMessenger');
        if(\KC\Repository\Widget::addWidget($params)) {
            $message = $this->view->translate('widget has been added successfully');
            $flash->addMessage(array('success' => $message));
            $this->_helper->redirector(null , 'widget' , null ) ;

        } else {
            $message = $this->view->translate('Problem in your data.');
            $flash->addMessage(array('error' => $message));
            $this->_helper->redirector(null , 'widget' , null ) ;
        }
    }
}


    /**
     * Get widget list from database
     * @author mkaur
     */
    public function widgetlistAction()
    {
        $params = $this->_getAllParams();
        $data = \KC\Repository\Widget::getWidgetList($params);
        echo Zend_Json::encode ($data);
        die ();

        /*echo Zend_Json::encode (DataTable_Helper::generateDataTableResponse
                ( $data,$params, array ("__identifier" => 'w.id','w.id','w.title'),array (),array()));
        die ();*/
    }

    /**
     * onlinestatus function changes the widget status
     * @author mkaur
     */
    public function onlinestatusAction()
    {
        $params = $this->_getAllParams();
        $id = \KC\Repository\Widget::changeStatus($params);
        self::updateVarnish($id);
        echo Zend_Json::encode ( $id );
        die ();
    }

    /**
     * editwidget function used for edit the existing widget
     * @author mkaur
     */
    public function editwidgetAction()
    {
    $id = intval($this->getRequest()->getParam('id'));
    $this->view->qstring = $_SERVER['QUERY_STRING'];
    $params = $this->_getAllParams();
    if(intval($id) > 0 ) {
        $data = \KC\Repository\Widget::updateWidget($id);
        $this->view->data = $data ;
        $this->view->id = $id;
    }
    if ($this->_request->isPost()) {
        $widget = new \KC\Repository\Widget();
        $flash = $this->_helper->getHelper('FlashMessenger');
        if($widget->editWidgetRecord($params)) {
            self::updateVarnish($id);
            $message = $this->view->translate('Widget has been updated successfully');
            $flash->addMessage(array('success' => $message));
            $this->_redirect(HTTP_PATH.'admin/widget#'.$this->getRequest()->getParam('qString'));

        } else {
            $message = $this->view->translate('Problem in your data.');
            $flash->addMessage(array('error' => $message));
            $this->_redirect(HTTP_PATH.'admin/widget#'.$this->getRequest()->getParam('qString'));
        }
    }
    if(@$params['act']=='delete'){
        $widget = new \KC\Repository\Widget();
        $flash = $this->_helper->getHelper('FlashMessenger');
        if($widget->permanentDeleteWidget($params['id'])) {
            $message = $this->view->translate('Widget has been deleted successfully');
            $flash->addMessage(array('success' => $message));
            $this->_helper->redirector(null , 'widget' , null ) ;
        } else {
            $message = $this->view->translate('Problem in your data.');
            $flash->addMessage(array('error' => $message));
            $this->_helper->redirector(null , 'widget' , null ) ;
        }
    }

}

/**
 * Search top five widgets from database based on search text
 * @author mkaur
 */
    public function searchkeyAction()
    {
        $srh = $this->getRequest()->getParam('keyword');
        $data = \KC\Repository\Widget::searchKeyword($srh);
        $ar = array();
        if (sizeof($data) > 0) {
            foreach ($data as $d) {
                    $ar[] = ucfirst($d['title']);
            }
        } else{
            $ar[] = "No Record Found.";
        }
        echo Zend_Json::encode($ar);
        die;
    }




    /**
     *  updateVarnish
     *
     *  update varnish table when a widget is updated and deleted
     *
     *  @param integer $id widget id
     *  @author Surinderpal Singh
     */
    public function updateVarnish($id)
    {
        // Add urls to refresh in Varnish
        $varnishObj = new \KC\Repository\Varnish();

        # get all the urls related to this shop
        $varnishUrls = \KC\Repository\Widget::getAllUrls($id);

        # check $varnishUrls has atleast one
        if(isset($varnishUrls) && count($varnishUrls) > 0) {
            foreach($varnishUrls as $value) {
                $varnishObj->addUrl( HTTP_PATH_FRONTEND . $value);
            }
        }
    }

}
