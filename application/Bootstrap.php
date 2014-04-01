<?php 

/**
 * Bootstrap
 *
 *load all function,view required for zend
 *
 *@author kraj
 *@version 1.0
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

	protected $lang = null ;
    protected $moduleNames = array();
    protected $request = null;
    protected $_httpHost = null;
    protected $_siteName = "kortingscode.nl" ;

    /**
     * Set base controller or view request
     *
     * @version 1.0
     */
    public function _initRequest()
    {
    	$this->bootstrap('frontController');
    	$front = $this->getResource('frontController');
    	$front->setRequest(new Zend_Controller_Request_Http());
    	$this->_request = $front->getRequest();
    	$this->_httpHost = $this->_request->getHttpHost();

    	Zend_Registry::set('request',$this->_request) ;
    	Zend_Registry::set('db_locale',false);

    	if(isset($_COOKIE['site_name']))
		{
			$this->_siteName = $_COOKIE['site_name'] ;
    	}

    }
    /**
     * _initSiteModules
     *
     * Set module level layout and doctype of rendered view
     *
     * @author kraj
     * @version1.0
     */
    protected function _initSiteModules() {
    	//Don't forget to bootstrap the front controller as the resource may not been created yet...
    	$this->bootstrap("frontController");
    	$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
    			'viewRenderer');
    	$viewRenderer->initView();
    	$viewRenderer->view->doctype('XHTML1_TRANSITIONAL');
    	$front = $this->getResource("frontController");
    	//Add modules dirs to the controllers for default routes...
    	$front->addModuleDirectory(APPLICATION_PATH . '/modules');

    }
    /**
     * set initial contand according to locale settings
     *
     */
	function _initContants()  {

		# get the front controller instance
		$front = Zend_Controller_Front::getInstance();
		$cd = $front->getControllerDirectory();
		$this->_moduleNames = array_keys($cd);
		$permalink = ltrim($_SERVER['REQUEST_URI'], '/');
		$routeProp = preg_split( "/[\/\?]+/" , $permalink ) ;
		$tempLang  = rtrim( $routeProp[0] , '/') ;
		if(in_array(strtolower($tempLang) , $this->_moduleNames)) {
			$this->_lang = strtolower($routeProp[0]);

		} else {

			$this->_lang = "default" ;
		}
		
		
		# define HTTP path
		define("HTTP_PATH", trim('http://' . $_SERVER['HTTP_HOST'] . '/'));

		# read cdn settings
		$cdnSettings = $this->getOption('cdn') ;

		# define cdn server http path

	 	if(isset($cdnSettings) && isset($cdnSettings[$_SERVER['HTTP_HOST']])){
			define("HTTP_PATH_CDN", trim('http://'. $cdnSettings[$_SERVER['HTTP_HOST']] . '/'));
			//define("HTTP_PATH_CDN", trim('http://' . $cdnSettings[$_SERVER['HTTP_HOST']] . '/public/'));
			
		} else {
			define("HTTP_PATH_CDN", trim('http://' . $_SERVER['HTTP_HOST'] . '/'));
			
			/*define("HTTP_PATH_CDN",
					trim('http://'. $cdnSettings[$_SERVER['HTTP_HOST']]
							.'/'. strtolower($this->_lang) .'/'));*/
		}

		# define path for load images from front-end / back-end 
		//define("HTTP_PATH_CDN", trim('http://' . $_SERVER['HTTP_HOST'] . '/public/'));

		# define root path
		defined('BASE_ROOT')
		|| define("BASE_ROOT", dirname($_SERVER['SCRIPT_FILENAME']) . '/' );

		if( strlen( strtolower($this->_lang)) == 2 && ($this->_httpHost != "kortingscode.nl" &&  $this->_httpHost != "www.kortingscode.nl")) {


			#constant for zend cache key based on lcoale
			define("LOCALE",  trim(strtolower($this->_lang)));

			# define LOCALE PATH for links
			define("HTTP_PATH_LOCALE", trim('http://' . $_SERVER['HTTP_HOST'] . '/' . $this->_lang .'/' ));

			# PUBLIC PATH
			defined('PUBLIC_PATH')
			|| define('PUBLIC_PATH',
					'http://' . $_SERVER['HTTP_HOST']
					. dirname($_SERVER['SCRIPT_NAME']) . '/'. strtolower($this->_lang) .'/');

			# define cdn server public path
		 	if(isset($cdnSettings) && isset($cdnSettings[$_SERVER['HTTP_HOST']])){
				define("PUBLIC_PATH_CDN", 
						trim('http://'. $cdnSettings[$_SERVER['HTTP_HOST']] 
							.'/'. strtolower($this->_lang) .'/'));
			} else {
				define("PUBLIC_PATH_CDN", 
						trim('http://' . $_SERVER['HTTP_HOST'] 
							. '/'. strtolower($this->_lang) .'/'));
			}



			# define root path
			defined('ROOT_PATH')
			|| define('ROOT_PATH', dirname($_SERVER['SCRIPT_FILENAME']) . '/' . strtolower($this->_lang) .'/');

			# define upload path
			defined('UPLOAD_PATH')
			|| define('UPLOAD_PATH', strtolower($this->_lang) .'/'. 'images/');

			# define upload image path
			defined('UPLOAD_IMG_PATH')
			|| define('UPLOAD_IMG_PATH', UPLOAD_PATH . 'upload/');


			# define upload path for excell
			defined('UPLOAD_EXCEL_PATH')
			|| define('UPLOAD_EXCEL_PATH', APPLICATION_PATH. '/../data/' . strtolower($this->_lang) .'/'. 'excels/');

			# define img path
			defined('IMG_PATH')
			|| define('IMG_PATH', PUBLIC_PATH . "images/"  );

		} else if(trim(strtolower($this->_lang)) == 'admin' ) {

			$lang = '';
			if(isset($_COOKIE['locale']) && ($_COOKIE['locale']) != 'en') {

				$lang = $_COOKIE['locale'] . "/";


				#constant for zend cache key based on lcoale
				define("LOCALE",  trim($lang , '/'));

			} else
			{
				#constant for zend cache key based on lcoale
				define("LOCALE", '');
 			}
 
			if(! defined('HTTP_PATH_FRONTEND') )
			{
				define("HTTP_PATH_FRONTEND", trim('http://www.' . $this->_siteName ."/"));
			}
			# define LOCALE PATH for links


			# PUBLIC PATH
			defined('PUBLIC_PATH')
			|| define('PUBLIC_PATH',
					'http://' . $_SERVER['HTTP_HOST']
					. dirname($_SERVER['SCRIPT_NAME']) . '/');


			# PUBLIC PATH with locale
			defined('PUBLIC_PATH_LOCALE')
			|| define('PUBLIC_PATH_LOCALE',
					'http://' . $_SERVER['HTTP_HOST']
					. dirname($_SERVER['SCRIPT_NAME']) . '/' . $lang);


			# define root path
			defined('ROOT_PATH')
			|| define('ROOT_PATH', dirname($_SERVER['SCRIPT_FILENAME']) . '/' . $lang);



			# define upload path
			defined('UPLOAD_PATH')
			|| define('UPLOAD_PATH',  'images/');

			# define upload path
			defined('UPLOAD_PATH1')
			|| define('UPLOAD_PATH1', $lang);

			# define upload image path
			defined('UPLOAD_IMG_PATH')
			|| define('UPLOAD_IMG_PATH', UPLOAD_PATH . 'upload/');


			# define upload path for excell
			defined('UPLOAD_EXCEL_PATH')
			|| define('UPLOAD_EXCEL_PATH', APPLICATION_PATH. '/../data/' . strtolower($lang) . 'excels/');



			# define imag path
			defined('IMG_PATH')
			|| define('IMG_PATH', PUBLIC_PATH . "images/"  );



			# PUBLIC PATH
			defined('HTTP_PATH_LOCALE')
			|| define('HTTP_PATH_LOCALE',
					'http://' . $_SERVER['HTTP_HOST']
					. dirname($_SERVER['SCRIPT_NAME']) . '/'. strtolower($this->_lang) .'/');

			//echo (PUBLIC_PATH_LOCALE);

		} else {

			#constant for zend cache key based on lcoale
			define("LOCALE", '');


			# define LOCALE PATH for links
			define("HTTP_PATH_LOCALE", trim('http://' . $_SERVER['HTTP_HOST'] . '/' ));

			# PUBLIC PATH
			defined('PUBLIC_PATH')
			|| define('PUBLIC_PATH',
					'http://' . $_SERVER['HTTP_HOST']
					. dirname($_SERVER['SCRIPT_NAME']) . '/');

			# define cdn server public path
		 	if(isset($cdnSettings) && isset($cdnSettings[$_SERVER['HTTP_HOST']])){
				define("PUBLIC_PATH_CDN", trim('http://'. $cdnSettings[$_SERVER['HTTP_HOST']] . '/'));
			} else {
				define("PUBLIC_PATH_CDN", trim('http://' . $_SERVER['HTTP_HOST'] . '/'));
			}

			# define root path
			defined('ROOT_PATH')
			|| define('ROOT_PATH', dirname($_SERVER['SCRIPT_FILENAME']) . '/');

			# define upload path
			defined('UPLOAD_PATH')
			|| define('UPLOAD_PATH', 'images/');

			# define upload image path
			defined('UPLOAD_IMG_PATH')
			|| define('UPLOAD_IMG_PATH', UPLOAD_PATH . 'upload/');

			#define upload excel path
			defined('UPLOAD_EXCEL_PATH')
			|| define('UPLOAD_EXCEL_PATH', 'excels/');

			# define image path
			defined('IMG_PATH')
			|| define('IMG_PATH', PUBLIC_PATH . "images/"  );
		}
		
	}

	/**
	 * Create connection with database by doctrine and
	 * defined model ,time zone and get dsn(doman name server)
	 * @return Ambigous <Doctrine_Connection, multitype:>
	 * @author kraj
	 * @version1.0
	 */
	protected function _initDoctrine() {

		$domain = $_SERVER['HTTP_HOST'];
		spl_autoload_register(array('Doctrine', 'modelsAutoload'));
		$manager = Doctrine_Manager::getInstance();
		//$manager->setAttribute(Doctrine_Core::ATTR_TBLNAME_FORMAT, $doctrineOptions["prefix"] . '_%s');
		$manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING,
						Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
		$manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
		$manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);

		Doctrine_Core::loadModels(APPLICATION_PATH . '/models');

		$doctrineOptions = $this->getOption('doctrine');

		$conn1 = Doctrine_Manager::connection($doctrineOptions["imbull"],
				"doctrine");


		//$locale = strlen($this->_lang) == 2 ? $this->_lang  : 'en' ;
		if(strlen($this->_lang) == 2) {

			$locale = $this->_lang ;

		}else if($this->_lang == 'admin'){

			$locale =  isset($_COOKIE['locale']) ? $_COOKIE['locale'] : 'en'  ;

		}else {

			$locale = 'en' ;
		}

		if((strlen($this->_lang) == 2) && $domain == "kortingscode.nl" || $domain == "www.kortingscode.nl"):
			$locale = 'en' ;
		endif;

		$conn2 = Doctrine_Manager::connection($doctrineOptions[strtolower($locale)]['dsn'],
							"doctrine_site");

		//echo $doctrineOptions[$locale]['dsn'] ;
		date_default_timezone_set('Asia/Calcutta');

		return $conn1;
	}

	/**
	 * Set the initial translation
	 *
	 * @author chetan
	 */
	function _initTranslation(){


		# add suffix according to locale
		$suffix = "" ;
		if(LOCALE)
		{
			$suffix = "_" . strtoupper( LOCALE)  ;
		}


		$domain = $_SERVER['HTTP_HOST'];

 		if(strlen($this->_lang) == 2 ) {
 			if($domain != "www.kortingscode.nl" && $domain != "kortingscode.nl" )
 			{
	 			$localePath = '/'.$this->_lang.'/' ;
 			} else {
 				$localePath = '/' ;
 			}
 		}else if($this->_lang == 'admin'){

			$localePath =  isset($_COOKIE['locale']) && $_COOKIE['locale'] != 'en' ? '/'.$_COOKIE['locale'].'/' : '/'  ;
		}else {
 			$localePath = '/' ;
 		}

 		$locale = Signupmaxaccount::getallmaxaccounts();
		$locale = !empty($locale[0]['locale']) ? $locale[0]['locale'] : 'nl_NL';


		$trans = new Zend_Translate(array(
						'adapter' => 'gettext',
						'disableNotices' => true));

		$trans->addTranslation(
				array(
						'content' => APPLICATION_PATH.'/../public'.strtolower($localePath).'language/frontend_php' . $suffix . '.mo',
						'locale' => $locale,
				)
		);

		$trans->addTranslation(
				array(
						'content' => APPLICATION_PATH.'/../public'.strtolower($localePath).'language/po_links' . $suffix . '.mo',
						'locale' => $locale,
				)
		);
		$trans->addTranslation(
				array(
						'content' => APPLICATION_PATH.'/../public'.strtolower($localePath).'language/backend_php' . $suffix. '.mo',
						'locale' => $locale,
				)
		);

		Zend_Registry::set('Zend_Translate', $trans);
		$locale = new Zend_Locale( $locale );
		Zend_Registry::set('Zend_Locale', $locale);
  		$date = new Zend_Date();
  		$month = $date->get(Zend_Date::MONTH_NAME);
  		$year = $date->get(Zend_Date::YEAR);
  		$day = $date->get(Zend_Date::DAY);


  		#define currecnt month for text with [month]
  		defined('CURRENT_MONTH')
  		|| define('CURRENT_MONTH', $month );

  		#define currecnt year for text with [year]
  		defined('CURRENT_YEAR')
  		|| define('CURRENT_YEAR', $year );

  		#define currecnt day for text with [day]
  		defined('CURRENT_DAY')
  		|| define('CURRENT_DAY', $day );
  	}

	/**
	 * initDocType
	 *
	 * Defined docoment type of view , meta description and head title of view
	 *
	 * @author kraj
	 * @version1.0
	 */
	protected function _initDocType() {

		$this->bootstrap('View');
		$view = $this->getResource('View');
		$view->doctype('HTML5');
		$view->headMeta()->appendHttpEquiv('Content-type', 'text/html; charset=UTF-8');
	}

	/**
	 * _initAutoLoad
	 *
	 * set all path of application,form,model etc
	 *
	 * @return Zend_Loader_Autoloader
	 * @author kraj
	 * @version1.0
	 */
	protected function _initAutoLoad() {

		$autoLoader = Zend_Loader_Autoloader::getInstance();
		$resourceLoader = new Zend_Loader_Autoloader_Resource(
				array('basePath' => APPLICATION_PATH,
						'namespace' => 'Application',
						'resourceTypes' => array(
								'form' => array('path' => 'forms/',
										'namespace' => 'Form'),
								'model' => array('path' => 'models/',
										'namespace' => 'Model'))));




		return $autoLoader;
	}

	/**
	 * _initRouter
	 *
	 * Route the URI to respective URL using Zend_Controller_Router_Route
	 *
	 * @author cbhopal
	 * @version1.0
	 */
	function _initRouter() {

		$url = '';
		//trim slashes from URL from right and left
		$permalink = ltrim($_SERVER['REQUEST_URI'], '/');
		$domain = $_SERVER['HTTP_HOST'];
		$permalink = rtrim($permalink, '/');

		#check if URI exist in route redirect table if this exists
		#make a 301 redirection moved permanently
		$data1 = RouteRedirect::getRoute(HTTP_PATH.$permalink);


		# we need the params after ?
		$query_params = strstr($permalink, '?');
		if (!empty($query_params)){
			$permalink = strstr($permalink, '?', true);
		}
		if (count($data1) > 0) {
			#  and get $newurl from your list
			$newurl =  $data1[0]['redirectto'];
			# set redirect code to 301 instead of default 302
			header ('Location: '.$newurl.$query_params, true, 301);
			exit();
		}

		$routeProp =  explode( '/' , $permalink  ) ;


		//var_dump($routeProp);
		if(count($routeProp) == 1) {

			$permalink = $routeProp[0] ;

		} elseif(count($routeProp) == 2) {

				if(intval($routeProp[0]) > 0) {

					$permalink = $routeProp[0] ;

				}else {

					preg_match('/^[1-3]{1}$/', $routeProp[1] , $mInt);
					if($mInt) {
						$permalink = $routeProp[0] ;
					}else{
						$permalink = $routeProp[1] ;
					}
				}
		}elseif (count($routeProp) == 3) {

			preg_match('/^[1-3]{1}$/', $routeProp[2] , $mInt);
			if($mInt) {

				$permalink = $routeProp[2] ;
			}

		}

		$search = '~([a-zA-z]+.)([\?].+)~';

		 $replace = '$1';

		 // $siteName = preg_search( $search, $replace, $hostPath);
		 preg_match( $search , $permalink , $res);

		 if($res)
		 {
			 $permalink = preg_replace( $search, $replace, $permalink);
		 }

	
		# get last word in permalink using regex match
		preg_match("/[^\/]+$/", $permalink, $matches);
		if(intval(@$matches[0]) > 0){

			$permalink = explode('/'.$matches[0],$permalink);
			$data = RoutePermalink::getRoute($permalink[0]);
			$permalink1 = $permalink[0];

		}else{

			$data = RoutePermalink::getRoute($permalink);
			$permalink1 = $permalink;
		}

		
		//$routeProp[0] = strtolower($routeProp[0]);
		# check if permalink exists in route permalink table
		if(count($data) > 0){


			# get the page detail from page table on the basis of permalink
			$pageDetail = RoutePermalink::getPageProperties(strtolower($data[0]['permalink']));
			$this->pageDetail = $pageDetail;

			//check if there exist page belongs to the permalink then append the
			//id of that page with actual URL
			if(!empty($pageDetail)) {

				$data[0]['exactlink'] = $data[0]['exactlink'].'/attachedpage/'.$this->pageDetail[0]['id'];
			}

	        //explode actual URL on the basis of slash
	        $url = explode('/', $data[0]['exactlink']);

	        //set the first and second element of an array as controller & action
	        //and generate $paramArray with all params required for routing purpose
			$paramArray = array(
					'controller' => @$url[0],
					'action'     => @$url[1]
			);

			//push extra parameters if required for routing in $paramArray
			for($u = 2; $u < count(@$url); $u++){
				if($u % 2 == 0){
					$paramArray[@$url[$u]] = @$url[$u+1];
				}
			}

			//append relative pageid in $paramArray
			if(!empty($pageDetail)) {
				$paramArray['attachedpage'] = $this->pageDetail[0]['id'];
			}

			//append page number for pagination if exist
			if(@$matches[0] > 0){

				//	$paramArray['page'] = @$matches[0];

			}

			if(in_array(strtolower($routeProp[0]), $this->_moduleNames)) {

				$lang = $routeProp[0] ;

				$paramArray['module'] = 'default';

				$paramArray['lang'] = $lang;

				//create an instance for zend router

				$router = Zend_Controller_Front::getInstance()->getRouter();

				if($domain == "kortingscode.nl" || $domain == "www.kortingscode.nl")
				{

					$router->addRoute('kortingscode', new Zend_Controller_Router_Route(

							'/:lang/*',

							array(

									'controller' => ':lang',
									'module' => 'default'
							)
					));

					return ;
				}

				//route redirection to relative route

				$route = new Zend_Controller_Router_Route($lang .'/'. $permalink1.'/*',$paramArray);

				$router->addRoute('user', $route);

				//echo "<pre>"; print_r($lang .'/'. $permalink1.'/*'); die;
				return;
			} else {

				//create an instance for zend router
				$router = Zend_Controller_Front::getInstance()->getRouter();
				//route redirection to relative route
				$route = new Zend_Controller_Router_Route($permalink1.'/*',$paramArray);
				$router->addRoute('user', $route);
			}


		}

		# for 301 redirections of old indexed pages
		if(is_array($permalink)){
			$permalinkToCheck = explode('/',$permalink[0]);
		}else{
			$permalinkToCheck = explode('/',$permalink);
		}

		switch ($permalinkToCheck[0]) {

			case "kortingen":
				$newpermalink = HTTP_PATH."nieuw";
				header ("Location: ".$newpermalink, true, 301);
				die();
				break;

			case "shops":
				$newpermalink = HTTP_PATH."store";
				header ("Location: ".$newpermalink, true, 301);
				die();
				break;


			case "producten":
				$newpermalink = HTTP_PATH."categorieen";
				header ("Location: ".$newpermalink, true, 301);
				die();
				break;

			case "rssfeeds":
			case "get-action-ratings":
			case "get-shop-rating":
			case "get-shop-reviews":
			case "get-shop-ratings":
			case "dynamics":
			case "2010":
			case "2011":
			case "2012":

				$newpermalink = HTTP_PATH;
				header ("Location: ".$newpermalink, true, 301);
				die();
				break;

		}

		$config = new Zend_Config_Ini(APPLICATION_PATH.'/configs/routes.ini', 'production');

		if($routeProp[0] != 'admin' && in_array(strtolower($routeProp[0]), $this->_moduleNames) ) {



			$frontController = Zend_Controller_Front::getInstance();
			$router = $frontController->getRouter();
			if($domain == "kortingscode.nl" || $domain == "www.kortingscode.nl")
			{

				$router->addRoute('kortingscode', new Zend_Controller_Router_Route(
						'/:lang/*',
						array(
								'controller' => ':lang',
								'module' => 'default'
						)
				));
				return ;
			}

			if(	$this->_request->isXmlHttpRequest())
			{


				$router->addRoute('xmlHttp', new Zend_Controller_Router_Route(
						'/:lang/:@controller/:@action/*',
						array(
								'action' => ':action',
								'controller' => ':controller',
								'module' => 'default'
						)
				));

				foreach($config->routes as $key => $r){
					switch($key)
					{

									case 'usermenu' :

									case 'userwidget' :

									case 'userfooter' :

									case 'usersignup' :


											$module  = isset($r->defaults->module) ? $r->defaults->module : 'default' ;

											$page = isset($r->defaults->page) ? 1 : null ;

											$router->addRoute("langmod_$key", new Zend_Controller_Router_Route(

													'/:lang/'.$r->route,

													array(

															'lang' => ':lang',

															'action' => $r->defaults->action,

															'controller' => $r->defaults->controller,

															'module' => $module,

													)

											));



										break;

								}
				}

			}

			$lang = $routeProp[0] ;


			$router = Zend_Controller_Front::getInstance()->getRouter();


			foreach($config->routes as $key => $r){

			

				if($r->type != "Zend_Controller_Router_Route_Regex")
				{
					$module  = isset($r->defaults->module) ? $r->defaults->module : 'default' ;
					$page = isset($r->defaults->page) ? 1 : null ;
					switch ($key) {
						case 'o2feed' :
							
							if($lang == 'pl' || $lang == 'in'){
								$router->addRoute("langmod_$key", new Zend_Controller_Router_Route(
										'/:lang/'.$r->route,
										array(
												'lang' => ':lang',
												'action' => 'top10.xml',
												'controller' => 'o2feed'
										)
								));
							}
							break;
						
						default:
							$router->addRoute("langmod_$key", new Zend_Controller_Router_Route(
										'/:lang/'.$r->route,
										array(
												'lang' => ':lang',
												'action' => $r->defaults->action,
												'controller' => $r->defaults->controller,
												'module' => $module,
												'page' => $page
										)
								));
							
							break;
					}
				} else {

					# base route for language params
					$lang = new Zend_Controller_Router_Route(
							':lang',
							array(
								'lang' => ':lang'
							)
					);

					# base route for redactie link translation
					$baseChain = new Zend_Controller_Router_Route(
								'@redactie',
								array(
									'controller' => 'about',
									'module' => 'default'
									)
						);


					switch($key)
					{
						
						case 'profilepage' :

							# validate page parameter with regex
							$page = new Zend_Controller_Router_Route_Regex(
									'^(\d?+)$',
									array( 'page' => '1','action' => 'index'),
									array( 1 => 'page' ),
									'%d'
							);

							# cretae page route chain
							$chainedRoute = new Zend_Controller_Router_Route_Chain();
							$pageChained = $chainedRoute->chain($lang)
											->chain($baseChain)
											->chain($page);
							# add routes to router
							$router->addRoute('redactier_page', $pageChained);

						break;
						case 'aboutdefault' :

							# validate slug parameter with regex i.e name of redactie
							$slug = new Zend_Controller_Router_Route_Regex(
									'^([a-zA-Z]+(?:-[a-zA-Z]+)?+)+$',
									array( 'slug' => '','action' => 'profile'),
									array( 1 => 'slug' ),
									'%d'
							);

							# cretae slug route chain
							$chainedRouteSlug = new Zend_Controller_Router_Route_Chain();
							$slugChained = $chainedRouteSlug->chain($lang)
											->chain($baseChain)
											->chain($slug);

							# add routes to router
							$router->addRoute('redactier_slug', $slugChained);
						break;
 					}

				}
			}
			return ;

		} else {

			$router1 = Zend_Controller_Front::getInstance()->getRouter();
			 # trigger error for flipt.com
			if($domain == "flipit.com" || $domain == "www.flipit.com")
			{
				$router1 = Zend_Controller_Front::getInstance()->getRouter();

		  	    $router1->addRoute("marktplaatsfeed", new Zend_Controller_Router_Route(
								'marktplaatsfeed',
								array(

										'action' => "error",
										'controller' => "error"

								)
				  ));

		  	    $router1->addRoute("metronieuws", new Zend_Controller_Router_Route(
		  	    		'metronieuws/top10.xml',
		  	    		array(
		  	    				'action' => "error",
		  	    				'controller' => "error"
		  	    		)
		  	    ));

		  	    $router1->addRoute("sargassofeed", new Zend_Controller_Router_Route(
								'sargassofeed',
								array(

										'action' => "error",
										'controller' => "error"

								)
				  ));
			}

			//route redirection instance for rules written in routes.ini
			$router = Zend_Controller_Front::getInstance()->getRouter();

			$router->addConfig(new Zend_Config_Ini(APPLICATION_PATH.'/configs/routes.ini', 'production'), 'routes');

		}

	}

	protected function _initCache(){

			$frontendOptions = array(
			   'lifetime' => 300,                   // cache lifetime
			   'automatic_serialization' => true
			);

			$backendOptions = array('cache_dir' => './tmp/');
			$cache = Zend_Cache::factory('Output',
			                             'File',
			                             $frontendOptions,
			                             $backendOptions);
			Zend_Registry::set('cache',$cache);
		}

    protected function _initMemcache()
    {
        if (extension_loaded('memcache'))
        { echo $_SERVER['SERVER_PORT']; die;
    //37.34.50.225
            $cacheBackend = new Zend_Cache_Backend_Memcached(
                array(
                    'servers' => array(
                        array(
                            'host' => 'localhost',
                            'port' => '80'
                        )
                        // Other servers here
                    ),
                    'compression' => true,
                    'compatibility' => true
                )
            );
            $cacheFrontend = new Zend_Cache_Core(
                array(
                    'caching' => true,
                    'cache_id_prefix' => 'MyApp_',
                    'write_control' => true,
                    'automatic_serialization' => true,
                    'ignore_user_abort' => true
                )
            );
            $memcache = Zend_Cache::factory($cacheFrontend, $cacheBackend);
            Zend_Registry::set('cache', $memcache);
        } else {
        }
    }
}


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
		 preg_match('/public/', $_SERVER['REQUEST_URI'], $matches, PREG_OFFSET_CAPTURE, 1);

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

