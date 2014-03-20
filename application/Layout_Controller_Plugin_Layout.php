<?php 
class Layout_Controller_Plugin_Layout extends Zend_Controller_Plugin_Abstract {
	
	protected $_moduleName = '';
	
	/**
	 * This function is called once after router shutdown. It automatically
	 * sets the layout for the default MVC or a module-specific layout. If
	 * you need to set a custom layout based on the controller called, you
	 * can set it here using a switch based on the action or controller or
	 * set the layout in the controller itself.
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 */
	public function routeShutdown(Zend_Controller_Request_Abstract $request) {
		
		$this->_moduleName = strtolower($request->getModuleName());
		$layout = Zend_Layout::getMvcInstance();
		$domain = $request->getHttpHost();
		$languageLocale   =   strtolower($request->getParam('lang')) ;
		# print in case public keyword exists in url
		//var_dump($request->getParams());
		
		preg_match('/public/', REQUEST_URI, $matches, PREG_OFFSET_CAPTURE, 1);
		
		if(count($matches) > 0) {
		    $this->_moduleName = 'default';
		}
		
		if(!empty($languageLocale) && $this->_moduleName == 'default') {
			
			if($domain == "kortingscode.nl" || $domain == "www.kortingscode.nl") {
				$layoutPath = APPLICATION_PATH .  '/layouts/scripts/' ;
			} else {
				//$layoutPath = APPLICATION_PATH . '/modules/'.$languageLocale.'/layouts/scripts/' ;
				$layoutPath = APPLICATION_PATH . '/layouts/scripts/' ;
			}

			$layout->setLayoutPath($layoutPath);

		} else if(empty($languageLocale) && $this->_moduleName) {
			
			if($this->_moduleName == 'default' || $this->_moduleName == null) {
				$layoutPath = APPLICATION_PATH . '/layouts/scripts/' ;
			} else {
				$layoutPath = APPLICATION_PATH . '/modules/'.$this->_moduleName.'/layouts/scripts/' ;
				//$layoutPath = APPLICATION_PATH . '/layouts/scripts/' ;
			}
			
			$layout->setLayoutPath($layoutPath);
		}

		# redirect to login page if url is flipit.com/ADMIN
		if($languageLocale == 'admin') {
			header ('Location: http://'.$request->getScheme .'/'. $request->getHttpHost() .'/admin', true, 301);
			exit();
		}

		$layout->setLayout('layout');
		$frontController = Zend_Controller_Front::getInstance();

        if (!($frontController->getPlugin('Zend_Controller_Plugin_ErrorHandler') 
        		instanceof Zend_Controller_Plugin_ErrorHandler)) {
        	
            return;
        }

        $errorHandler = $frontController->getPlugin('Zend_Controller_Plugin_ErrorHandler');
        
        $httpRequest = new Zend_Controller_Request_Http();
        $httpRequest->setModuleName($request->getModuleName())
                    ->setControllerName($errorHandler->getErrorHandlerController())
                    ->setActionName($errorHandler->getErrorHandlerAction());
        
        if ($frontController->getDispatcher()->isDispatchable($httpRequest)) {
            $errorHandler->setErrorHandlerModule($request->getModuleName());
        }
        
        return ;

        if (!$frontController->getDispatcher()->isDispatchable($request)) {

        	if($this->_moduleName == 'default' && empty($languageLocale) &&
        			($request->getHttpHost() == "www.flipit.com" 
        			||	$request->getHttpHost() == "flipit.com" ) ) {
        		
        		header ('Location: http://'.$request->getScheme .'/'. $request->getHttpHost(), true, 301);
        		
        		exit();
        	}
        }
    }

    /**
     * This function is called after the request is dispatch to a controller.
     * We validate logge in user based upon the locale
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
    	
    	$languageLocale   =   strtolower($request->getParam('lang')) ;
    	# lang is only vailble in case of flipit
    	if(isset( $languageLocale ) &&  !empty($languageLocale)) {
    		
    		if(Auth_VisitorAdapter::hasIdentity()) {
    			# get visitor locale
	    		$visitorCurrentLocale =  Auth_VisitorAdapter::getIdentity()->currentLocale ;
    			if($visitorCurrentLocale != $languageLocale) {
		    		$request->setControllerName('login')->setActionName('logout');
		    	}
    		}
    	}
    	
    	$actionName   = strtolower($request->getActionName());
    	# log every request from cms
    	if($this->_moduleName === 'admin' ) {
    		
			self::cmsActivityLog($request);
			$sessionNamespace = new Zend_Session_Namespace();
			#force user to chnage his password if it's older than two months
			if($sessionNamespace->showPasswordChange && $actionName != 'logout') {
				if(Auth_StaffAdapter::hasIdentity()) {
					$request->setControllerName('Auth')->setActionName('update-password');
				}
			}

    	}
    }

    /**
     * cmsActivityLog
     *
     * write log all post request from cms regardless http or xmlHttp request
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    function cmsActivityLog($request) {

    		# ignore if a request is from datatable
	    	$requestFromDatatable = $request->getParam('iDisplayStart') ;

	    	if($request->isPost() && $requestFromDatatable == null) {

	    		$adminActivityForLogs = $request->getParams() ;
	    		unset($adminActivityForLogs['module']);
	     		# hide password fields
	    		$replacements = array('pwd'=> '********',
	    				'oldPassword'=> '********',
	    				'newPassword'=> '********',
	    				'confirmNewPassword'=> '********',
	    				'password'=> '********',
	    				'confPassword'=> '********');

	    		foreach($replacements as $k => $v) {
	    			if(array_key_exists($k, $adminActivityForLogs)) {
	    				$adminActivityForLogs[$k] = $v;
	    			}
	    		}

	    		$adminActivityForLogs = Zend_Json::encode($adminActivityForLogs) ;
	    		
	    		$logStorageLocation = APPLICATION_PATH . "/../logs/";
	    		# create directory if it isn't exists and write log file
	    		if(!file_exists( $logStorageLocation  ))
	    		mkdir( $logStorageLocation , 776, TRUE);

	    		$fileName = $logStorageLocation  . 'cms';
	    		
	    		$requestURI = $request->getRequestUri();
	    		$emailOfCurrentUser = '';
	    		
	    		if(Auth_StaffAdapter::hasIdentity()) {
		    		$emailOfCurrentUser = ";" . Auth_StaffAdapter::getIdentity()->email ;
	    		}	else	{
	    			$emailOfCurrentUser = ";" ;
	    		}
	    		
			    $locale = LOCALE == '' ? "kc" : LOCALE ;
		    	# please avoid to format below template (like tab or space )
$requestLog = <<<EOD
                {$locale}{$emailOfCurrentUser};{$requestURI}; $adminActivityForLogs
EOD;
	    		FrontEnd_Helper_viewHelper::writeLog($requestLog , $fileName ) ;
    	}
    }
} 
