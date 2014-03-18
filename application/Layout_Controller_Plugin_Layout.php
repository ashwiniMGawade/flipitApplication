<?php 
class Layout_Controller_Plugin_Layout extends Zend_Controller_Plugin_Abstract {

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

		$layout = Zend_Layout::getMvcInstance();
		$module   = strtolower($request->getModuleName());
		$controller = strtolower($request->getControllerName());
		$action     = strtolower($request->getActionName());
		$domain = $request->getHttpHost();
		$frontController = Zend_Controller_Front::getInstance();


		# print in case public keyword exists in url
		 preg_match('/public/', REQUEST_URI, $matches, PREG_OFFSET_CAPTURE, 1);

		 if(count($matches) > 0)
		 {
		 	$module = 'default';
		 }

		$lang   =   strtolower($request->getParam('lang')) ;
		//var_dump($request->getParams());
		//die;

		if(! empty($lang) && $module == 'default' )
		{
			if($domain == "kortingscode.nl" || $domain == "www.kortingscode.nl")
			{
				$layoutPath = APPLICATION_PATH .  '/layouts/scripts/' ;
			} else {
				$layoutPath = APPLICATION_PATH . '/modules/'.$lang.'/layouts/scripts/' ;
			}

			$layout->setLayoutPath( $layoutPath  );

		}else if(  empty($lang) && $module ) {
			if($module == 'default' || $module == null) {
				$layoutPath = APPLICATION_PATH . '/layouts/scripts/' ;
			} else {
				$layoutPath = APPLICATION_PATH . '/modules/'.$module.'/layouts/scripts/' ;
			}
			$layout->setLayoutPath( $layoutPath  );
		}


		# redirect to login page if url is flipit.com/ADMIN
		if($lang == 'admin')
		{
			header ('Location: http://'.$request->getScheme .'/'. $request->getHttpHost() .'/admin', true, 301);
			exit();
		}


		$layout->setLayout('layout');
		$front = Zend_Controller_Front::getInstance();

        if (!($front->getPlugin('Zend_Controller_Plugin_ErrorHandler') instanceof Zend_Controller_Plugin_ErrorHandler)) {
            return;
        }


        $error = $front->getPlugin('Zend_Controller_Plugin_ErrorHandler');
        $httpRequest = new Zend_Controller_Request_Http();
        $httpRequest->setModuleName($request->getModuleName())
                    ->setControllerName($error->getErrorHandlerController())
                    ->setActionName($error->getErrorHandlerAction());
        if ($front->getDispatcher()->isDispatchable($httpRequest)) {
            $error->setErrorHandlerModule($request->getModuleName());
        }
        return ;


        $front = Zend_Controller_Front::getInstance();
        if (!$front->getDispatcher()->isDispatchable($request)) {

        	if($module == 'default' && empty($lang) &&
        			($request->getHttpHost() == "www.flipit.com" ||	$request->getHttpHost() == "flipit.com" ) )
        	{
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
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
    	$lang   =   strtolower($request->getParam('lang')) ;

    	# lang is only vailble in case of flipit
    	if(isset( $lang ) &&  !empty($lang))
    	{

    		# check user is logges in
    		if(Auth_VisitorAdapter::hasIdentity())
    		{
    			# get visitor locale
	    		$vLocale =  Auth_VisitorAdapter::getIdentity()->currentLocale ;

    			# compare locale and lang for visitor session validation
		    	if($vLocale != $lang)
		    	{
		    		$request->setControllerName('login')->setActionName('logout');
		    	}
    		}
    	}


    	$module   = strtolower($request->getModuleName());
    	$action   = strtolower($request->getActionName());

    	# log every request from cms
    	if($module === 'admin' )
    	{
			self::cmsActivityLog($request);

			$sessionNamespace = new Zend_Session_Namespace();

			#force user to chnage his password if it's older than two months
			if($sessionNamespace->showPasswordChange && $action != 'logout')
			{
				# check user is logges in
				if(Auth_StaffAdapter::hasIdentity())
				{
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
    function cmsActivityLog($request)
    {

    		# ignore if a request is from datatable
	    	$isDatatablRequest = $request->getParam('iDisplayStart') ;

	    	if($request->isPost() && $isDatatablRequest == null)
	    	{

    			# get params and convret them into json
	    		$requestParams = $request->getParams() ;

	    		unset($requestParams['module']);

	     		# hide password fields
	    		$replacements = array(  'pwd' => '********',
	    				'oldPassword' => '********',
	    				'newPassword' => '********',
	    				'confirmNewPassword' => '********',
	    				'password' => '********',
	    				'confPassword' => '********' );

	    		foreach($replacements as $k => $v) {
	    			if(array_key_exists($k, $requestParams)) {
	    				$requestParams[$k] = $v;
	    			}
	    		}

	    		$requestParams = Zend_Json::encode($requestParams) ;

	    		# log directory path
	    		$logDir = APPLICATION_PATH . "/../logs/";


	    		# create directory if it isn't exists and write log file
	    		if(!file_exists( $logDir  ))
	    		mkdir( $logDir , 776, TRUE);


	    		$fileName = $logDir  . 'cms';

	    		# request url
	    		$requestURI = $request->getRequestUri();

	    		$email = '';
	    		# check user is logges in
	    		if(Auth_StaffAdapter::hasIdentity())
	    		{
		    		# get visitor locale
		    		$email = ";" . Auth_StaffAdapter::getIdentity()->email ;
	    		}	else	{
	    			$email = ";" ;
	    		}

			    $locale = LOCALE == '' ? "kc" : LOCALE ;

		    	# please avoid to format below template (like tab or space )
		    	$requestLog = <<<EOD
				{$locale}{$email};{$requestURI}; $requestParams
EOD;
	    		FrontEnd_Helper_viewHelper::writeLog($requestLog , $fileName ) ;

    	}
    }
}