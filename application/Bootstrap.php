<?php
require_once 'BootstrapConstantsFunctions.php';
require_once 'BootstrapAdminConstantsFunctions.php';
require_once 'BootstrapLocaleConstantsFunctions.php';
require_once 'BootstrapDoctrineConnectionFunctions.php';
require_once 'BootstrapTranslationFunctions.php';
require_once 'BootstrapRouterFunctions.php';
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected $moduleDirectoryName = null;
    protected $moduleName = array();
    protected $request = null;
    protected $httpHost = null;
    protected $siteName = "kortingscode.nl";
    protected $route = null;
    protected $routeProperties = array();
    protected $cdnUrl = '';
    protected $scriptFileName = '';
    protected $scriptName = '';
    protected $localeCookieData = '';
    public $frontController = null;

    public function _initRequest()
    {
        $this->bootstrap('frontController');
        $this->frontController = $this->getResource('frontController');
        $this->frontController->setRequest(new Zend_Controller_Request_Http());
        $this->request = $this->frontController->getRequest();
        $this->httpHost = $this->request->getHttpHost();
        $this->route = Zend_Controller_Front::getInstance()->getRouter();
        Zend_Registry::set('request', $this->request);
        Zend_Registry::set('db_locale', false);
        $cookieData = $this->request->getCookie('site_name');
        if (isset($cookieData) && !empty($cookieData)) {
            $this->siteName = $cookieData;
        }
        $this->scriptFileName = $this->request->getServer('SCRIPT_FILENAME');
        $this->scriptName = $this->request->getServer('SCRIPT_NAME');
        $this->localeCookieData = $this->request->getCookie('locale');
    }

    protected function _initControllerHelpers()
    {
        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH .'/controllers/helpers');
    }

    protected function _initSiteModules()
    {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->initView();
        $viewRenderer->view->doctype('XHTML1_TRANSITIONAL');
        $this->frontController->addModuleDirectory(APPLICATION_PATH . '/modules');
    }

    public function _initContants()
    {
        BootstrapConstantsFunctions::constantsForSettingRequestHeaders();
        $routeUrl = ltrim(REQUEST_URI, '/');
        $this->routeProperties = preg_split('/[\/\?]+/', $routeUrl);
        $routeUrlWithoutSlash  = rtrim($this->routeProperties[0], '/');
        $moduleDirectoryNames = $this->frontController->getControllerDirectory();
        $this->moduleName = array_keys($moduleDirectoryNames);

        if (in_array(strtolower($routeUrlWithoutSlash), $this->moduleName)) {
            $this->moduleDirectoryName = strtolower($this->routeProperties[0]);
        } else {
            $this->moduleDirectoryName = "default";
        }

        BootstrapConstantsFunctions::constantForCacheDirectory($this->getOption['CACHE_DIRECTORY_PATH']);
        $this->cdnUrl = $this->getOption('cdn');
        BootstrapConstantsFunctions::httpPathConstantForCdn($this->cdnUrl, 'HTTP_PATH_CDN');
        $s3Credentials = $this->getOption('s3');
        BootstrapConstantsFunctions::s3ConstantDefines($s3Credentials);
        defined('BASE_ROOT') || define('BASE_ROOT', dirname($this->scriptFileName) . '/');
        self::setContantsForLocaleAndAdmin();
    }

    public function setContantsForLocaleAndAdmin()
    {
        if (strlen(strtolower($this->moduleDirectoryName))==2 && $this->httpHost != "www.kortingscode.nl") {
            BootstrapLocaleConstantsFunctions::constantsForLocale(
                $this->moduleDirectoryName,
                $this->scriptName,
                $this->cdnUrl,
                $this->scriptFileName
            );
        } elseif (trim(strtolower($this->moduleDirectoryName)) == 'admin') {
            BootstrapAdminConstantsFunctions::constantsForAdminModule(
                $this->localeCookieData,
                $this->siteName,
                $this->scriptName,
                $this->scriptFileName,
                $this->moduleDirectoryName,
                $this->cdnUrl
            );
        } else {
            BootstrapConstantsFunctions::constantsForDefaultModule(
                $this->scriptName,
                $this->cdnUrl,
                $this->scriptFileName
            );
        }
        BootstrapConstantsFunctions::constantsForFacebookImageAndLocale();
    }

    protected function _initAutoLoad()
    {
        $autoLoader = Zend_Loader_Autoloader::getInstance();
        $resourceLoader = new Zend_Loader_Autoloader_Resource(
            array(
                'basePath' => APPLICATION_PATH,
                'namespace' => 'Application',
                'resourceTypes' => array(
                    'form' => array('path' => 'forms/', 'namespace' => 'Form'),
                    'model' => array('path' => 'models/', 'namespace' => 'Model'),
                    'service' => array('path' => 'services/', 'namespace' => 'Service')
                )
            )
        );
        return $autoLoader;
    }
     public function _initAutoloaderNamespaces()
     {
         require_once APPLICATION_PATH.'/../library/Doctrine/Common/ClassLoader.php';

         $autoloader = \Zend_Loader_Autoloader::getInstance();
         $symfonyAutoloader = new \Doctrine\Common\ClassLoader('Symfony', 'Doctrine');
         $autoloader->pushAutoloader(array($symfonyAutoloader, 'loadClass'), 'Symfony');
     }
    protected function _initViewScripts()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('HTML5');
        $view->addHelperPath(APPLICATION_PATH . '/../library/Transl8/View/Helper/', 'Transl8_View_Helper_');
        return $view;
    }

    protected function _initDocType()
    {
        $this->bootstrap('View');
        $view = $this->getResource('View');
        $view->doctype('HTML5');
        $view->headMeta()->appendHttpEquiv('Content-type', 'text/html; charset=UTF-8');
    }


    public function setRoutersByRules($permalink, $httpScheme)
    {
        // for 301 redirections of old indexed pages
        BootstrapRouterFunctions::redirectionForOldWebsiteUrls($permalink);
        if ($this->routeProperties[0] != 'admin' &&
            in_array(strtolower($this->routeProperties[0]), $this->moduleName)) {
            BootstrapRouterFunctions::setRouteIfModuelNotExist($this->route, $httpScheme);
            BootstrapRouterFunctions::setRouteForLocale($this->request, $this->route, $this->routeProperties);
        } else {
            if (HTTP_HOST == $httpScheme.'.flipit.com') {
                BootstrapRouterFunctions::errorRouteForFlipit($this->route);
            }
            BootstrapRouterFunctions::getRouteFromRuleFile($this->route);
        }
        return;
    }

}
require_once 'Layout_Controller_Plugin_Layout.php';
