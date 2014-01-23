<?php
/**
 * This controller handle all the activisties regarding chain  management of shops
 * 
 * @author spsingh1
 *
 */
class Admin_ChainController extends Zend_Controller_Action
{
	/**
	 * check authentication before load the page
	 */
	public function preDispatch() {
	
		$conn2 = BackEnd_Helper_viewHelper::addConnection (); 
		
		$params = $this->_getAllParams ();
		if (! Auth_StaffAdapter::hasIdentity ()) {
			$referer = new Zend_Session_Namespace('referer');
			$referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			$this->_redirect ( '/admin/auth/index' );
		}
		
		BackEnd_Helper_viewHelper::closeConnection ( $conn2 );
		
		$this->view->controllerName = $this->getRequest ()->getParam ( 'controller' );
		$this->view->action = $this->getRequest ()->getParam ( 'action' );
	
		$sessionNamespace = new Zend_Session_Namespace();
		
		if($sessionNamespace->settings['rights']['administration']['rights'] != '1' && $sessionNamespace->settings['rights']['administration']['rights'] !='2' )
		{
			
			$flash = $this->_helper->getHelper('FlashMessenger');
			$message = $this->view->translate ( 'You have no permission to access page' );
			$flash->addMessage ( array ('error' => $message ));
			$this->_redirect ( '/admin' );
		}
	}
	
    public function indexAction()
    {
        $flash = $this->_helper->getHelper ( 'FlashMessenger' );
    	$message = $flash->getMessages ();
    	$this->view->messageSuccess = isset ( $message [0] ['success'] ) ? $message [0] ['success'] : '';
    	$this->view->messageError = isset ( $message [0] ['error'] ) ? $message [0] ['error'] : '';
    }
    
    
    public function addShopAction()
    {
    	
    	# requets object 
    	$request = $this->getRequest();
    	
    	$chianId = $request->getParam('chain' , false);
    	
    	   	
    	if($chianId)
    	{
    		
			$this->view->websites = Website::getAllwebSites();
	    	
	    	$this->view->chainId = $chianId ;
	    	
	    	$flash = $this->_helper->getHelper('FlashMessenger');
	    	$message = $flash->getMessages();
	    	$this->view->messageSuccess = isset($message[0]['success']) ? $message[0]['success'] : '';
	    	$this->view->messageError = isset($message[0]['error']) ? $message[0]['error'] : '';
	    	 
	    	 
	    	if ($this->_request->isPost())
	    	{
	    			
	    		$localeId = $request->getParam('locale' , false);
	    		
	    		# get selected locale detail
	    		$website = Website::getWebsiteDetail($localeId);
	    		
	    		$localeData = explode('/', $website['name']);
	    		$locale = isset($localeData[1]) ?  $localeData[1] : "en" ;
	    		
	    		# connect to select locale database
	    		$connObj = BackEnd_Helper_DatabaseManager::addConnection($locale);
	    		 
	    		$signMaxObj = new Signupmaxaccount($locale);
	    		$langLocale =  $signMaxObj::getallmaxaccounts();
	    		$langLocale = !empty($langLocale[0]['locale']) ? $langLocale[0]['locale'] : 'nl_NL';
	    		
	    		
	    		Zend_Registry::set('db_locale', $locale ) ;
	    		    		
	    		# save new chain
	    		$chain = new ChainItem();
	    		$ret = $chain->saveChain($request,$langLocale);
	    
	    		# if chain is saved then refresh shop page in varnish 
	    		if($ret)
	    		{
	        		$message = $this->view->translate ( 'Shop has been added successfully' );
		    		$flash->addMessage ( array ('success' => $message ));
		    	} else {
		    
			    	$message = $this->view->translate ( 'This shop has been already added for this particulat locale' );
			    	$flash->addMessage ( array ('error' => $message ));
		    	}
		    	
		    	
		    	# close connection
		    	$connObj = BackEnd_Helper_DatabaseManager::closeConnection($connObj['adapter']);
		    	
		    	Zend_Registry::set('db_locale', false ) ;
		    	
    			$this->_redirect ( HTTP_PATH . 'admin/chain/chain-item/chain/'. $chianId  );
	    	}
    	} else {
    		$this->_redirect ( HTTP_PATH . 'admin/chain' );
    	}
    	 
	}
    
    public function shopsListAction()
    {
    	
    	# validate a request is json request
    	if(	$this->_request->isXmlHttpRequest())
    	{
	    	# check valid locale id
    		$id =  intval($this->getRequest()->getParam('locale', false ));
    		
    		if($id)
    		{
    			# get selected locale and create 
    			 
		    	$website = Website::getWebsiteDetail($id);
		    	
		    	$localeData = explode('/', $website['name']);
		    	
		    	$locale = isset($localeData[1]) ?  $localeData[1] : "en" ;
		    	
		    	
		    	$connObj = BackEnd_Helper_DatabaseManager::addConnection($locale);
		    	
		    	$shops = new Shop($connObj['connName']);
		    	
		    	$key = $this->getRequest()->getParam('keyword');
		    	
		    	
		    	# get shop data return an array
		    	$shopsData = $shops->getAllShopNames($key);
		    	
		    	$connObj = BackEnd_Helper_DatabaseManager::closeConnection($connObj['adapter']);
		    	
		    	
		    	$shops = array();
				if (sizeof($shopsData) > 0) {

		    		foreach ($shopsData as $shop) {
		    			$shops[] = array('name' => ucfirst($shop['name']),
		    							 'id' => $shop['id']);
		    		}
		    	
		    	} else {
		    		 
		    		$msg = $this->view->translate('No Record Found');
		    		$shops[] = array('name' => $msg);
		    	}
		    	
				$this->_helper->json($shops);
    		}
    	
    	}
    	$this->_redirect ( '/admin' );
    	
    }
    
    
    
    /**
     * get all chains from database and display in a list
     * 
     * @author sp singh
     */
    
    public function chainListAction()
    {
    	$params = $this->_getAllParams();

    	$chains =  Chain::returnChainList($params);
    	
    	$this->_helper->json($chains);
    }
    
    public function chainItemAction()
    {
    	
    	$flash = $this->_helper->getHelper ( 'FlashMessenger' );
    	$message = $flash->getMessages ();
    	$this->view->messageSuccess = isset ( $message [0] ['success'] ) ? $message [0] ['success'] : '';
    	$this->view->messageError = isset ( $message [0] ['error'] ) ? $message [0] ['error'] : '';
    	
    	
    	$id = $this->getRequest()->getParam('chain',false);
    	
    	# validate for valid integr chain id
    	if(intval($id) > 0)
    	{
	    	$chain =  Chain::returnChainDetail($id);
    		
	    	$this->view->chain = $chain;
    	}else
    	{
    		$this->_redirect ( HTTP_PATH . 'admin/chain' );
    	}
    	
    }
    
    
    /**
     * get all chains from database and display in a list
     *
     * @author sp singh
     */
    
    public function chainItemListAction()
    {
    	$params = $this->_getAllParams();
    
    	$chains =  ChainItem::returnChainItemList($params);
    	 
    	$this->_helper->json($chains);
    }
    
    
    
    public function deleteChainAction()
    {
    	$id = $this->getRequest()->getParam('id',false);
    	
    	if($id)
    	{
    		
    		$data = Chain::deleteChain($id);
    		
    		$flash = $this->_helper->getHelper('FlashMessenger');
    		
    		if($data)
    		{
    			$message = $this->view->translate('Chain has been deleted successfully');
    			$flash->addMessage(array('success' => $message));
    			$this->_helper->json(true);
    		}  

    		$message = $this->view->translate('Problem in your data.');
    		$flash->addMessage(array('error' => $message));
    		
    		$this->_helper->json(false);
    	}
    	
    	
    	$message = $this->view->translate('Please try again later.');
    	$flash->addMessage(array('error' => $message));
    	
    	$this->_helper->json(false);
    }
    
    
    public function deleteChainItemAction()
    {
    	$id = $this->getRequest()->getParam('id',false);
    	 
    	if($id)
    	{
    		
			if(ChainItem::deleteChainItem($id))
    		{
    			 
    			$flash = $this->_helper->getHelper('FlashMessenger');
    			$message = $this->view->translate('Shop has been deleted successfully');
    			$flash->addMessage(array('success' => $message));
    			$this->_helper->json(true);
    		}
    
    		$message = $this->view->translate('Problem in your data.');
    		$flash->addMessage(array('error' => $message));
    
    		$this->_helper->json(false);
    	}
    	 
    	 
    	$message = $this->view->translate('Please try again later.');
    	$flash->addMessage(array('error' => $message));
    	 
    	$this->_helper->json(false);
    }
    
    
    
    /**
     * add new chain
     *
     */
    public function addChainAction()
    {
    	
		if ($this->_request->isPost())
    	{
    		# save new chain
    		$chain = new Chain();
    		$ret = $chain->saveChain($this->getRequest());
    
    		$flash = $this->_helper->getHelper ( 'FlashMessenger' );
    
    		if($ret)
    		{
	    		$message = $this->view->translate ( 'Chain has been saved successfully' );
	    		$flash->addMessage ( array ('success' => $message ));
	    	} else {
	    
		    	$message = $this->view->translate ( 'Duplicate chain name' );
		    	$flash->addMessage ( array ('error' => $message ));
	    	 
	    	}
    		$this->_redirect ( HTTP_PATH . 'admin/chain' );
    	}
    	 
    }
    
}





