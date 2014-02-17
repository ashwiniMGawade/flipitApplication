<?php

/**
 * SiteMaps Generation
 *
 * @author Surinderpal Singh
 *
 */
class CreateSiteMaps {
	
	protected $_localePath = '/';
	protected $_hostName = '';
	protected $_trans = null;
	
	
	function __construct() {

	ini_set('memory_limit', '-1');
		
	set_time_limit(0);
		
	/*
	$domain1 = $_SERVER['HOSTNAME'];
	$domain = 'http://www.'.$domain1;
	*/

	// Define path to application directory
	defined('APPLICATION_PATH')
	|| define('APPLICATION_PATH',
			dirname(dirname(__FILE__)));
	
	defined('LIBRARY_PATH')
	|| define('LIBRARY_PATH', realpath(dirname(dirname(dirname(__FILE__))). '/library'));
	
	defined('DOCTRINE_PATH') || define('DOCTRINE_PATH', LIBRARY_PATH . '/Doctrine');
	
	// Define application environment
	defined('APPLICATION_ENV')
	|| define('APPLICATION_ENV',
			(getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV')
					: 'production'));
	
	
	//Ensure library/ is on include_path
	set_include_path(
			implode(PATH_SEPARATOR,
					array(realpath(APPLICATION_PATH . '/../library'),
							get_include_path(),)));
		set_include_path(
				implode(PATH_SEPARATOR,
						array(realpath(DOCTRINE_PATH), get_include_path(),)));
		
		/** Zend_Application */
		
		//echo LIBRARY_PATH;
		//echo DOCTRINE_PATH;
		//die;
		require_once(LIBRARY_PATH.'/FrontEnd/Helper/viewHelper.php');
		require_once (LIBRARY_PATH . '/Zend/Application.php');
		require_once(DOCTRINE_PATH . '/Doctrine.php');
		
		// Create application, bootstrap, and run
		$application = new Zend_Application(APPLICATION_ENV,
				APPLICATION_PATH . '/configs/application.ini');
		
		$connections = $application->getOption('doctrine');
		spl_autoload_register(array('Doctrine', 'autoload'));
		
		$manager = Doctrine_Manager::getInstance();

		$imbull = $connections['imbull'];
		
		// cycle htoruh all site database
		foreach ( $connections as $key => $connection ) {
			// check database is being must be site
			if ($key != 'imbull') {
				try {
						
					$this->generateMaps ( $connection ['dsn'], $key ,$imbull );
				} catch ( Exception $e ) {
						
					echo $e->getMessage ();
					echo "\n\n";
				}
				echo "\n\n";
			}
		}
	}
	
	protected function generateMaps($dsn, $key,$imbull) {
		
		
		if ($key == 'en') {
			$this->_localePath = '';
			$this->_hostName = "http://www.kortingscode.nl";
			$this->_logo = $this->_hostName . "/public/images/front_end/logo-popup.png";
			$suffix = "" ;
		} else {
			$this->_localePath = $key . "/";
			$this->_hostName = "http://www.flipit.com";
			$suffix = "_" . strtoupper($key) ;
		}
		
		$domainForRobot = $this->_hostName . '/public/' . $this->_localePath;
		
		
		defined('PUBLIC_PATH')
		|| define('PUBLIC_PATH',
				dirname(dirname(dirname(__FILE__)))."/public/");
		
		$pathToXMLFile = PUBLIC_PATH . $this->_localePath;
		
		
		$DMC = Doctrine_Manager::connection($dsn, 'doctrine_site');
		spl_autoload_register(array('Doctrine', 'modelsAutoload'));
		
		$manager = Doctrine_Manager::getInstance();
		//Doctrine_Core::loadModels(APPLICATION_PATH . '/models/generated');
		
		$manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
		$manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
		$manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
		Doctrine_Core::loadModels(APPLICATION_PATH . '/models');
		
		$cutsomLocale = Signupmaxaccount::getallmaxaccounts ();
		$cutsomLocale = ! empty ( $cutsomLocale [0] ['locale'] ) ? $cutsomLocale [0] ['locale'] : 'nl_NL';
		
		$this->_trans = new Zend_Translate ( array ('adapter' => 'gettext', 'disableNotices' => true ) );
		
		defined ( 'PUBLIC_PATH' ) || define ( 'PUBLIC_PATH', dirname ( dirname ( dirname ( __FILE__ ) ) ) . "/public/" );
		
		
		# add suffix according to locale
		$this->_trans->addTranslation ( array ('content' => PUBLIC_PATH . strtolower ( $this->_localePath ) . 'language/po_links' . $suffix . '.mo', 'locale' => $cutsomLocale ) );
		
		
	
		$pathToXMLFile = PUBLIC_PATH . $this->_localePath;
		
		
		Zend_Registry::set('Zend_Translate', $this->_trans);
		//setting to no time limit,
		set_time_limit(0);
		//declaring class instance
		$sitemap = new PHPSitemap_sitemap();
		
		#translating sitemaps names
		$sitemaps = FrontEnd_Helper_viewHelper::__link('sitemap');
		$bespaarwijzer = FrontEnd_Helper_viewHelper::__link('bespaarwijzer');
		$main = FrontEnd_Helper_viewHelper::__link('main');
		$shops = FrontEnd_Helper_viewHelper::__link('shops');
	
		$info = FrontEnd_Helper_viewHelper::__link('info');
		$rssfeed = FrontEnd_Helper_viewHelper::__link('rssfeed');
		$zoeken = FrontEnd_Helper_viewHelper::__link('zoeken');
	
		$sitemap_shops = $sitemaps.'_'.$shops.'.xml';
		$sitemap_bespaarwijzer = $sitemaps.'_'.$bespaarwijzer.'.xml';
		$sitemap_main = $sitemaps.'_'.$main.'.xml';
	
		//submitting site map to Google, Yahoo, Bing, Ask and Moreover services
		//$sitemap->ping("http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	
		//Create robot.txt file
	
		$robotTextContent ="User-agent: *\r\nDisallow:/".$info."/\r\nDisallow:/".$rssfeed."/\r\nDisallow:/out/\r\nDisallow:/".$zoeken."/\r\nDisallow:/admin/\r\n\r\nSitemap:".$domainForRobot."sitemaps/".$sitemap_shops."\r\nSitemap:".$domainForRobot."sitemaps/".$sitemap_bespaarwijzer."\r\nSitemap:".$domainForRobot."sitemaps/".$sitemap_main;
	
		$robotTextFile = $pathToXMLFile."robots.txt";
		$robotTxtHandle = fopen($robotTextFile, 'w');
		fwrite($robotTxtHandle, $robotTextContent);
		print "$key - Robot.txt has been created!!!";
		fclose($robotTxtHandle);
	
		//End Create robot.txt file
	
		//generating sitemap shops
		$shopmap = $sitemap->generate_shops_sitemap($this->_hostName, $key);
		$mainDir = $pathToXMLFile."sitemaps/";
	
		if(!file_exists($mainDir))
			mkdir($mainDir, 776, TRUE);
	
		$shopFile = $pathToXMLFile."sitemaps/".$sitemap_shops;
		$shopHandle = fopen($shopFile, 'w');
		fwrite($shopHandle, $shopmap);
	
		echo "\n";
		print "$key - Sitemap for Online shops has been created successfully!!!";
		fclose($shopHandle);
	
		//generating sitemap Bespaarwijzers
		$guidemap = $sitemap->generate_guides_sitemap($this->_hostName, $key);
		$guideFile = $pathToXMLFile."sitemaps/".$sitemap_bespaarwijzer;
		$guideHandle = fopen($guideFile, 'w');
		fwrite($guideHandle, $guidemap);
	
		echo "\n";
		print "$key - Sitemap for Guides has been created successfully!!!";
		fclose($guideHandle);
	
		
		$DMC1 = Doctrine_Manager::connection($imbull, 'doctrine');
		
		
		//generating Main sitemap
		$mainmap = $sitemap->generate_main_sitemap($this->_hostName, $key);
		$mainFile = $pathToXMLFile."sitemaps/".$sitemap_main;
		$mainHandle = fopen($mainFile, 'w');
		fwrite($mainHandle, $mainmap);
	
		$manager->closeConnection($DMC);
		
		
		echo "\n";
		print "$key - Main sitemap has been created successfully!!!";
		fclose($mainHandle);
		
		
	}
		
}


new CreateSiteMaps ();
		
?>