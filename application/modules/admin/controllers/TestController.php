<?php

class Admin_TestController extends Zend_Controller_Action
{

	/**
	 * check authentication before load the page
	 * @see Zend_Controller_Action::preDispatch()
	 * @author kraj
	 * @version 1.0
	 */
	public function preDispatch() {
		$conn2 = BackEnd_Helper_viewHelper::addConnection();//connection generate with second database
		$params = $this->_getAllParams();
		if (!Auth_StaffAdapter::hasIdentity()) {
			$referer = new Zend_Session_Namespace('referer');
			$referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			$this->_redirect('/admin/auth/index');
		}
		BackEnd_Helper_viewHelper::closeConnection($conn2);
		$this->view->controllerName = $this->getRequest()->getParam('controller');
		$this->view->action = $this->getRequest()->getParam('action');


	}


    public function init()
    {
        /* Initialize action controller here */
		$this->_redirect('/admin/auth/index');
    }

    public function indexAction()
    {
        // action body

 		$data = $this->getRequest()->getParam('data');

    	$this->view->data = FrontEnd_Helper_viewHelper::sanitize( $data);

        $this->view->data =  FrontEnd_Helper_viewHelper::replaceStringVariable($this->view->data);


        echo str_replace("admin.","",HTTP_PATH);
        $sessionNamespace = new Zend_Session_Namespace();

        //var_dump($sessionNamespace->settings['rights']['content']['rights'] == '1');
		echo "<pre>";
      //  print_r($sessionNamespace->settings['rights']);
		echo "/<pre>";


		$a = new Varnish();


    }


}

