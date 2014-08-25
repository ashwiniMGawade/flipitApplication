<?php
require_once 'BootstrapApplicationConstants.php';
require_once 'BootstrapConstantsFunctions.php';
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
    protected $route = array();
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
        BootstrapConstantsFunctions::httpPathConstantForCdn($this->cdnUrl);
        $s3Credentials = $this->getOption('s3');
        BootstrapConstantsFunctions::s3ConstantDefines($s3Credentials);
        defined('BASE_ROOT') || define('BASE_ROOT', dirname($this->scriptFileName) . '/');
        if (strlen(strtolower($this->moduleDirectoryName))==2 && $this->httpHost != "www.kortingscode.nl") {
            BootstrapConstantsFunctions::constantsForLocale(
                $this->moduleDirectoryName,
                $this->scriptName,
                $this->cdnUrl,
                $this->scriptFileName
            );
        } elseif (trim(strtolower($this->moduleDirectoryName)) == 'admin') {
            BootstrapConstantsFunctions::constantsForAdminModule(
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

    protected function _initDoctrine()
    {
        return BootstrapDoctrineConnectionFunctions::doctrineConnection(
            $this->getOption('doctrine'),
            $this->moduleDirectoryName,
            $this->localeCookieData
        );
    }

    protected function _initAutoLoad()
    {
        $autoLoader = Zend_Loader_Autoloader::getInstance();
        $resourceLoader = new Zend_Loader_Autoloader_Resource(
            array(
                'basePath' => APPLICATION_PATH,
                'namespace' => 'Application',
                'resourceTypes' => array(
                    'form' => array(
                        'path' => 'forms/',
                        'namespace' => 'Form'
                    ),
                    'model' => array(
                        'path' => 'models/',
                        'namespace' => 'Model'
                    ),
                    'service' => array(
                        'path' => 'services/',
                        'namespace' => 'Service'
                    )
                )
            )
        );
        return $autoLoader;
    }

    public function _initTranslation()
    {
        BootstrapTranslationFunctions::setTranslationInZendRegistery(
            $this->request->getServer('HTTP_HOST'),
            $this->moduleDirectoryName,
            $this->localeCookieData
        );
    }

    protected function _initPluginLiveTranslation()
    {
        BootstrapTranslationFunctions::translationLivePlugin(
            $this->request->getServer('HTTP_HOST'),
            $this->moduleDirectoryName,
            $this->localeCookieData
        );
    }

    protected function _initI18n()
    {
        BootstrapTranslationFunctions::setTranslationFilesForLocale(
            $this->request->getServer('HTTP_HOST'),
            $this->moduleDirectoryName,
            $this->localeCookieData
        );
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

    public function _initRouter()
    {
        $permalink = BootstrapRouterFunctions::getPermalink();
        $this->routeProperties = explode('/', $permalink);
        $permalink = BootstrapRouterFunctions::splitRouteProperties($permalink, $this->routeProperties);
        $permalink = BootstrapRouterFunctions::replacePermalinkString($permalink);
        $httpScheme = FrontEnd_Helper_viewHelper::getServerNameScheme();
        // get last word in permalink using regex match
        preg_match('/[^\/]+$/', $permalink, $matches);
        // if url match with database permalink then get permalink from database and add in zend route
        // if not then check url exist in redirect then make a 301 redirect
         $matches = isset($matches[0]) ? $matches[0] : 0;
        if (intval($matches) > 0) {
            $permalink = explode('/'.$matches[0], $permalink);
            $getPermalinkFromDb = RoutePermalink::getRoute($permalink[0]);
            $actualPermalink = $permalink[0];
        } else {
            $getPermalinkFromDb = RoutePermalink::getRoute($permalink);
            $actualPermalink = $permalink;
        }

        // check if permalink exists in route permalink table
        if (count($getPermalinkFromDb) > 0) {
            BootstrapRouterFunctions::setRouteForPermalink(
                $getPermalinkFromDb,
                $actualPermalink,
                $this->routeProperties,
                $this->route,
                $this->moduleName
            );
        }
        // for 301 redirections of old indexed pages
        BootstrapRouterFunctions::redirectionForOldWebsiteUrls($permalink);
        if ($this->routeProperties[0] != 'admin' &&
            in_array(strtolower($this->routeProperties[0]), $this->moduleName)) {
            BootstrapRouterFunctions::setRouteIfModuelNotExist($this->route, $httpScheme);
            BootstrapRouterFunctions::setRouteForLocale($this->request, $this->route, $this->routeProperties);
        } else {
             // trigger error for flipt.com
            if (HTTP_HOST == $httpScheme.'.flipit.com') {
                BootstrapRouterFunctions::errorRouteForFlipit($this->route);
            }
            //route redirection instance for rules written in routes.ini
            BootstrapRouterFunctions::getRouteFromRuleFile($this->route);
        }
    }

    protected function _initCache()
    {
        $frontendOptions = array(
           'lifetime' => 300,
           'automatic_serialization' => true
        );
        $backendOptions = array('cache_dir' => CACHE_DIRECTORY_PATH);
        $cache = Zend_Cache::factory(
            'Output',
            'File',
            $frontendOptions,
            $backendOptions
        );
        Zend_Registry::set('cache', $cache);
    }
}
require_once 'Layout_Controller_Plugin_Layout.php';
