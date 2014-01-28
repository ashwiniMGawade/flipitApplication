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

 

		echo "<pre>";
		
		
		$input = array("a", "b", "c", "d", "e");
		
		
		
		$output = array_slice($input, 2);      // returns "c", "d", and "e"
		$output = array_slice($input, -2, 1);  // returns "d"
		$output = array_slice($input, 0, 3);   // returns "a", "b", and "c"
		
		// note the differences in the array keys
		print_r(array_slice($input, 2, -1));
		print_r(array_slice($input, 2, -1, true));

		
		echo "----------------\n";
		
 
		//array_chunk($visitors, 1000);
		//print_r(array_chunk($input_array, 2, true));


    }
    
    
    


}

