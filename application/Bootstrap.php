<?php
require_once 'BootstrapConstant.php';

/**
 *load all function,view required for zend
 *
 *@version 1.0
 *
 */

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected $moduleDirectoryName = null;
    protected $moduleName = array();
    protected $request = null;
    protected $httpHost = null;
    protected $siteName = "kortingscode.nl" ;
    protected $route ='';
    protected $routeProperties = '';
    protected $cdnUrl = '';
    public $frontController = '';

    /**
     * Set base controller or view request
     *
     * @version 1.0
     */
    public function _initRequest()
    {
        $this->bootstrap('frontController');
        $this->frontController = $this->getResource('frontController');
        $this->frontController->setRequest(
            new Zend_Controller_Request_Http()
        );
        $this->request = $this->frontController->getRequest();
        $this->httpHost = $this->request->getHttpHost();
        $this->route = Zend_Controller_Front::getInstance()->getRouter();
        Zend_Registry::set('request', $this->request);
        Zend_Registry::set('db_locale', false);
        if (isset($_COOKIE['site_name'])) {
            $this->siteName = $_COOKIE['site_name'] ;
        }
    }

    /**
     * _initSiteModules
     *
     * Set module level layout and doctype of rendered view
     *
     */
    protected function _initSiteModules()
    {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->initView();
        $viewRenderer->view->doctype('XHTML1_TRANSITIONAL');
        $this->frontController->addModuleDirectory(APPLICATION_PATH . '/modules');
    }

    /**
     * set initial content according to locale settings
     *
     */
    public function _initContants()
    {
        $routeUrl = ltrim(REQUEST_URI, '/');
        $this->routeProperties = preg_split("/[\/\?]+/", $routeUrl);
        $routeUrlWithoutSlash  = rtrim($this->routeProperties[0], '/');
        $moduleDirectoryNames = $this->frontController->getControllerDirectory();
        $this->moduleName = array_keys($moduleDirectoryNames);

        if (in_array(strtolower($routeUrlWithoutSlash), $this->moduleName)) {
            $this->moduleDirectoryName = strtolower($this->routeProperties[0]);
        } else {
            $this->moduleDirectoryName = "default" ;
        }
        self::constantForCacheDirectory();
        self::httpPathConstantForCdn();

        defined('BASE_ROOT') || define("BASE_ROOT", dirname($_SERVER['SCRIPT_FILENAME']) . '/');

        if (strlen(strtolower($this->moduleDirectoryName))==2 && $this->httpHost != "www.kortingscode.nl") {
            self::constantForLocale();
        } elseif (trim(strtolower($this->moduleDirectoryName)) == 'admin') {
            self::constantForAdminModule();
        } else {
            self::constantForDefaultModule();
        }
    }

    public function constantForCacheDirectory()
    {
        define("HTTP_PATH", trim('http://' . HTTP_HOST . '/'));
        $environment = $this->getOption('ENV');
        if ($environment == 'dev') {
            define("CACHE_DIRECTORY_PATH", $this->getOption['CACHE_DIRECTORY_PATH']);
        } else {
            define("CACHE_DIRECTORY_PATH", './tmp/');
        }
    }

    public function httpPathConstantForCdn()
    {
        $this->cdnUrl = $this->getOption('cdn');
        if (isset($this->cdnUrl) && isset($this->cdnUrl[HTTP_HOST])) {
            define("HTTP_PATH_CDN", trim('http://'. $this->cdnUrl[HTTP_HOST] . '/'));
        } else {
            define("HTTP_PATH_CDN", trim('http://' . HTTP_HOST . '/'));
        }
    }

    public function constantForLocale()
    {
        define("LOCALE", trim(strtolower($this->moduleDirectoryName)));
        define(
            "HTTP_PATH_LOCALE",
            trim('http://' . HTTP_HOST . '/' . $this->moduleDirectoryName .'/')
        );
        defined('PUBLIC_PATH')
        || define(
            'PUBLIC_PATH',
            'http://' . HTTP_HOST. dirname(
                $_SERVER['SCRIPT_NAME']
            ) . '/'.strtolower($this->moduleDirectoryName) .'/'
        );

        if (isset($this->cdnUrl) && isset($this->cdnUrl[HTTP_HOST])) {
            define(
                "PUBLIC_PATH_CDN",
                trim(
                    'http://'. $this->cdnUrl[HTTP_HOST]
                    .'/'. strtolower($this->moduleDirectoryName) .'/'
                )
            );
        } else {
            define(
                "PUBLIC_PATH_CDN",
                trim(
                    'http://' . HTTP_HOST
                    . '/'. strtolower($this->moduleDirectoryName) .'/'
                )
            );
        }

        defined('ROOT_PATH')
        || define(
            'ROOT_PATH',
            dirname($_SERVER['SCRIPT_FILENAME']) . '/'
            . strtolower($this->moduleDirectoryName) .'/'
        );

        defined('UPLOAD_PATH')
        || define(
            'UPLOAD_PATH',
            strtolower($this->moduleDirectoryName) .'/'. 'images/'
        );

        defined('UPLOAD_IMG_PATH')
        || define('UPLOAD_IMG_PATH', UPLOAD_PATH . 'upload/');

        defined('UPLOAD_EXCEL_PATH')
        || define(
            'UPLOAD_EXCEL_PATH',
            APPLICATION_PATH. '/../data/' . strtolower($this->moduleDirectoryName) .'/'. 'excels/'
        );

        defined('IMG_PATH')
        || define('IMG_PATH', PUBLIC_PATH . "images/");
    }

    public function constantForAdminModule()
    {
        $localAbbreviation = '';

        if (isset($_COOKIE['locale']) && ($_COOKIE['locale']) != 'en') {
            $localAbbreviation = $_COOKIE['locale'] . "/";
            define("LOCALE", trim($localAbbreviation, '/'));

        } else {
            define("LOCALE", '');
        }

        if (! defined('HTTP_PATH_FRONTEND')) {
            define("HTTP_PATH_FRONTEND", trim('http://www.' . $this->siteName ."/"));
        }

        defined('PUBLIC_PATH')
        || define(
            'PUBLIC_PATH',
            'http://' . HTTP_HOST
            . dirname($_SERVER['SCRIPT_NAME']) . '/'
        );

        defined('PUBLIC_PATH_LOCALE')
        || define(
            'PUBLIC_PATH_LOCALE',
            'http://' . HTTP_HOST
            . dirname($_SERVER['SCRIPT_NAME']) . '/' . $localAbbreviation
        );

        defined('ROOT_PATH')
        || define(
            'ROOT_PATH',
            dirname($_SERVER['SCRIPT_FILENAME']) . '/' . $localAbbreviation
        );

        defined('UPLOAD_PATH')
        || define('UPLOAD_PATH', 'images/');

        defined('UPLOAD_PATH1')
        || define('UPLOAD_PATH1', $localAbbreviation);

        defined('UPLOAD_IMG_PATH')
        || define('UPLOAD_IMG_PATH', UPLOAD_PATH . 'upload/');

        defined('UPLOAD_EXCEL_PATH')
        || define(
            'UPLOAD_EXCEL_PATH',
            APPLICATION_PATH. '/../data/' . strtolower($localAbbreviation) . 'excels/'
        );

        defined('IMG_PATH')
        || define('IMG_PATH', PUBLIC_PATH . "images/");

        defined('HTTP_PATH_LOCALE')
        || define(
            'HTTP_PATH_LOCALE',
            'http://' . HTTP_HOST
            . dirname($_SERVER['SCRIPT_NAME']) . '/'. strtolower($this->moduleDirectoryName) .'/'
        );
    }

    public function constantForDefaultModule()
    {
        define("LOCALE", '');
        define("HTTP_PATH_LOCALE", trim('http://' . HTTP_HOST . '/'));
        defined('PUBLIC_PATH')
        || define(
            'PUBLIC_PATH',
            'http://' . HTTP_HOST
            . dirname($_SERVER['SCRIPT_NAME']) . '/'
        );

        if (isset($this->cdnUrl) && isset($this->cdnUrl[HTTP_HOST])) {
            define("PUBLIC_PATH_CDN", trim('http://'. $this->cdnUrl[HTTP_HOST] . '/'));
        } else {
            define("PUBLIC_PATH_CDN", trim('http://' . HTTP_HOST . '/'));
        }

        defined('ROOT_PATH')
        || define('ROOT_PATH', dirname($_SERVER['SCRIPT_FILENAME']) . '/');

        defined('UPLOAD_PATH')
        || define('UPLOAD_PATH', 'images/');

        defined('UPLOAD_IMG_PATH')
        || define('UPLOAD_IMG_PATH', UPLOAD_PATH . 'upload/');

        defined('UPLOAD_EXCEL_PATH')
        || define('UPLOAD_EXCEL_PATH', 'excels/');

        defined('IMG_PATH')
        || define('IMG_PATH', PUBLIC_PATH . "images/");
    }

    /**
     * Create connection with database by doctrine and
     * defined model ,time zone and get dsn(doman name server)
     * @return Ambigous <Doctrine_Connection, multitype:>
     */
    protected function _initDoctrine()
    {
        $domain = HTTP_HOST;
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));
        $manager = Doctrine_Manager::getInstance();
        $manager->setAttribute(
            Doctrine_Core::ATTR_MODEL_LOADING,
            Doctrine_Core::MODEL_LOADING_CONSERVATIVE
        );
        $manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        Doctrine_Core::loadModels(APPLICATION_PATH . '/models');
        $doctrineOptions = $this->getOption('doctrine');
        $imbullDbConnection = Doctrine_Manager::connection(
            $doctrineOptions["imbull"],
            "doctrine"
        );

        $localSiteDbConnection =
            Doctrine_Manager::connection(
                $doctrineOptions[strtolower(self::getLocaleNameForDbConnection())]['dsn'],
                "doctrine_site"
            );
        date_default_timezone_set('Europe/Amsterdam');

        return $imbullDbConnection;
    }

    public function getLocaleNameForDbConnection()
    {
        if (strlen($this->moduleDirectoryName) == 2) {
            $locale = $this->moduleDirectoryName;
        } elseif ($this->moduleDirectoryName == 'admin') {
            $locale =  isset($_COOKIE['locale']) ? $_COOKIE['locale'] : 'en';
        } elseif ($this->moduleDirectoryName == "default") {
            $locale = 'en';
        }

        return $locale;
    }

    /**
     * Set the initial translation
     */
    public function _initTranslation()
    {
        $localeNameForTranslateFile = "" ;
        if (LOCALE) {
            $localeNameForTranslateFile = "_" .strtoupper(LOCALE);
        }

        $localeName = Signupmaxaccount::getLocaleName();
        $languageCultureName = !empty($localeName[0]['locale']) ? $localeName[0]['locale'] : 'nl_NL';

        Zend_Registry::set(
            'Zend_Translate',
            self::getTrablationFileObject($localeNameForTranslateFile, $languageCultureName)
        );
        $languageCultureName = new Zend_Locale($languageCultureName);
        Zend_Registry::set('Zend_Locale', $languageCultureName);
        $date = new Zend_Date();
        $month = $date->get(Zend_Date::MONTH_NAME);
        $year = $date->get(Zend_Date::YEAR);
        $day = $date->get(Zend_Date::DAY);
        defined('CURRENT_MONTH')
        || define('CURRENT_MONTH', $month);
        defined('CURRENT_YEAR')
        || define('CURRENT_YEAR', $year);
        defined('CURRENT_DAY')
        || define('CURRENT_DAY', $day);
    }

    public function getTrablationFileObject($localeNameForTranslateFile, $languageCultureName)
    {
        $zendTranslate = new Zend_Translate(
            array(
                'adapter' => 'gettext',
                'disableNotices' => true
            )
        );

        $languageFilePath = APPLICATION_PATH.'/../public'.strtolower(self::getLocaleNameForTranslation()).'language/';

        $zendTranslate->addTranslation(
            array(
                'content' => $languageFilePath.'frontend_php'
                . $localeNameForTranslateFile . '.mo',
                'locale' => $languageCultureName,
            )
        );
        $zendTranslate->addTranslation(
            array(
                'content' => $languageFilePath.'po_links'
                . $localeNameForTranslateFile . '.mo',
                'locale' => $languageCultureName,
            )
        );
        $zendTranslate->addTranslation(
            array(
               'content' => $languageFilePath.'backend_php'
               . $localeNameForTranslateFile. '.mo',
               'locale' => $languageCultureName,
           )
        );

        return $zendTranslate;
    }

    public function getLocaleNameForTranslation()
    {
        if (strlen($this->moduleDirectoryName) == 2) {
            if (HTTP_HOST != "www.kortingscode.nl") {
                $localePath = '/'.$this->moduleDirectoryName.'/' ;
            } else {
                $localePath = '/' ;
            }
        } elseif ($this->moduleDirectoryName == 'admin') {
            $localePath =  isset($_COOKIE['locale'])
            && $_COOKIE['locale'] != 'en' ? '/'.$_COOKIE['locale'].'/' : '/'  ;
        } else {
            $localePath = '/' ;
        }

        return $localePath;
    }

    /**
     * initDocType
     *
     * Defined docoment type of view , meta description and head title of view
     */
    protected function _initDocType()
    {
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
     */
    protected function _initAutoLoad()
    {
        $autoLoader = Zend_Loader_Autoloader::getInstance();
        $resourceLoader = new Zend_Loader_Autoloader_Resource(
            array(
                'basePath' => APPLICATION_PATH,
                'namespace' => 'Application',
                'resourceTypes' => array(
                    'form' => array('path' => 'forms/',
                        'namespace' => 'Form'),
                        'model' => array('path' => 'models/',
                            'namespace' => 'Model'
                        )
                    )
                )
        );

        return $autoLoader;
    }

    /**
     * _initRouter
     *
     * Route the URI to respective URL using Zend_Controller_Router_Route
     *
     */
    public function _initRouter()
    {
        $permalink = self::getPermalink();

        $this->routeProperties =  explode('/', $permalink);

        $permalink  =  self::splitRouteProperties($permalink);

        $permalink = self::replacePermalinkString($permalink);

        preg_match("/[^\/]+$/", $permalink, $matches);

        if (intval(@$matches[0]) > 0) {
            $permalink = explode('/'.$matches[0], $permalink);
            $getPermalinkInfoFromDb = RoutePermalink::getRoute($permalink[0]);
            $actualPermalink = $permalink[0];
        } else {
            $getPermalinkInfoFromDb = RoutePermalink::getRoute($permalink);
            $actualPermalink = $permalink;
        }

        # check if permalink exists in route permalink table
        if (count($getPermalinkInfoFromDb) > 0) {
            self::setRouteForPermalink($getPermalinkInfoFromDb, $actualPermalink);
        }
        # for 301 redirections of old indexed pages
        self::redirectionForOldIndexes($permalink);

        if ($this->routeProperties[0] != 'admin' &&
            in_array(strtolower($this->routeProperties[0]), $this->moduleName)) {

                self::setRouteForLocale();

        } else {
             # trigger error for flipt.com
            if (HTTP_HOST == "www.flipit.com") {
                self::errorRouteForFlipit();
            }
            //route redirection instance for rules written in routes.ini
            $this->route->addConfig(
                new Zend_Config_Ini(
                    APPLICATION_PATH.'/configs/routes.ini',
                    'production'
                ),
                'routes'
            );
        }
    }

    public function getPermalink()
    {
        $permalink = ltrim(REQUEST_URI, '/');
        $permalink = rtrim($permalink, '/');
        $routeRedirectName = RouteRedirect::getRoute(HTTP_PATH.$permalink);
        $splitPermalinkFromQueryString = strstr($permalink, '?');

        self::redirectUrl($routeRedirectName, $splitPermalinkFromQueryString);

        if (!empty($splitPermalinkFromQueryString)) {
            $permalink = strstr($permalink, '?', true);
        }

        return $permalink;
    }

    public function redirectUrl($routeRedirectName, $splitPermalinkFromQueryString)
    {
        if (count($routeRedirectName) > 0) {
            $redirectUrl =  $routeRedirectName[0]['redirectto'];
            header('Location: '.$redirectUrl.$splitPermalinkFromQueryString, true, 301);
            exit();
        }
    }

    public function splitRouteProperties($permalink)
    {
        if (count($this->routeProperties) == 1) {
            $permalink = $this->routeProperties[0] ;
        } elseif (count($this->routeProperties) == 2) {
            if (intval($this->routeProperties[0]) > 0) {
                $permalink = $this->routeProperties[0] ;
            } else {
                preg_match('/^[1-3]{1}$/', $this->routeProperties[1], $maximumIntegerNumber);
                if ($maximumIntegerNumber) {
                    $permalink = $this->routeProperties[0] ;
                } else {
                    $permalink = $this->routeProperties[1] ;
                }
            }
        } elseif (count($this->routeProperties) == 3) {
            preg_match('/^[1-3]{1}$/', $this->routeProperties[2], $maximumIntegerNumber);
            if ($maximumIntegerNumber) {
                $permalink = $this->routeProperties[2] ;
            }
        }

        return $permalink;

    }

    public function replacePermalinkString($permalink)
    {
        $searchString = '~([a-zA-z]+.)([\?].+)~';
        $replaceString = '$1';

        preg_match($searchString, $permalink, $resultString);

        if ($resultString) {
            $permalink = preg_replace($searchString, $replaceString, $permalink);
        }

        return $permalink;
    }

    public function setRouteForPermalink($getPermalinkInfoFromDb, $actualPermalink)
    {
        # get the page detail from page table on the basis of permalink
        $pageDetail = RoutePermalink::getPageProperties(
            strtolower($getPermalinkInfoFromDb[0]['permalink'])
        );
        $this->pageDetail = $pageDetail;
        //check if there exist page belongs to the permalink then append the
        //id of that page with actual URL
        if (!empty($pageDetail)) {
            $getPermalinkInfoFromDb[0]['exactlink'] =
            $getPermalinkInfoFromDb[0]['exactlink'].'/attachedpage/'.$this->pageDetail[0]['id'];
        }
        $parmalinkUrl = explode('/', $getPermalinkInfoFromDb[0]['exactlink']);
        $paramArray = array(
                'controller' => @$parmalinkUrl[0],
                'action'     => @$parmalinkUrl[1]
        );
        for ($u = 2; $u < count(@$parmalinkUrl); $u++) {
            if ($u % 2 == 0) {
                $paramArray[@$parmalinkUrl[$u]] = @$parmalinkUrl[$u+1];
            }
        }
        if (!empty($pageDetail)) {
            $paramArray['attachedpage'] = $this->pageDetail[0]['id'];
        }
        if (in_array(strtolower($this->routeProperties[0]), $this->moduleName)) {

            $paramArray['module'] = 'default';
            $paramArray['lang'] = $this->routeProperties[0];

            self::routeForDefaultModule();

            $route = new Zend_Controller_Router_Route(
                $this->routeProperties[0] .'/'. $actualPermalink .'/*',
                $paramArray
            );
            $this->route->addRoute('user', $route);

            return;
        } else {
            $route = new Zend_Controller_Router_Route($actualPermalink.'/*', $paramArray);
            $this->route->addRoute('user', $route);
        }

        return;
    }

    public function routeForDefaultModule()
    {
        if (HTTP_HOST == "www.kortingscode.nl") {
            $this->route->addRoute(
                'kortingscode',
                new Zend_Controller_Router_Route(
                    '/:lang/*',
                    array(
                        'controller' => ':lang',
                        'module' => 'default'
                    )
                )
            );
            return ;
        }
    }

    public function redirectionForOldIndexes($permalink)
    {
        # for 301 redirections of old indexed pages
        if (is_array($permalink)) {
            $permalinkToCheck = explode('/', $permalink[0]);
        } else {
            $permalinkToCheck = explode('/', $permalink);
        }

        switch ($permalinkToCheck[0]) {
            case "kortingen":
                $newPermalink = HTTP_PATH."nieuw";
                header("Location: ".$newPermalink, true, 301);
                die();
                break;
            case "shops":
                $newPermalink = HTTP_PATH."store";
                header("Location: ".$newPermalink, true, 301);
                die();
                break;
            case "producten":
                $newPermalink = HTTP_PATH."categorieen";
                header("Location: ".$newPermalink, true, 301);
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
                $newPermalink = HTTP_PATH;
                header("Location: ".$newPermalink, true, 301);
                die();
                break;
        }

    }
    public function setRouteForLocale()
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH.'/configs/routes.ini', 'production');
        
        self::routeForDefaultModule();
        
        if ($this->request->isXmlHttpRequest()) {
            $this->route->addRoute(
                'xmlHttp',
                new Zend_Controller_Router_Route(
                    '/:lang/:@controller/:@action/*',
                    array(
                        'action' => ':action',
                        'controller' => ':controller',
                        'module' => 'default'
                    )
                )
            );
            foreach ($config->routes as $key => $r) {
                switch ($key) {
                    case 'usermenu':
                    case 'userwidget':
                    case 'userfooter':
                    case 'usersignup':
                        $module  = isset($r->defaults->module) ? $r->defaults->module : 'default' ;
                        $page = isset($r->defaults->page) ? 1 : null ;
                        $this->route->addRoute(
                            "langmod_$key",
                            new Zend_Controller_Router_Route(
                                '/:lang/'.$r->route,
                                array(
                                    'lang' => ':lang',
                                    'action' => $r->defaults->action,
                                    'controller' => $r->defaults->controller,
                                    'module' => $module,
                                )
                            )
                        );
                        break;
                }
            }

        }

        foreach ($config->routes as $key => $r) {
            if ($r->type != "Zend_Controller_Router_Route_Regex") {
                $module  = isset($r->defaults->module) ? $r->defaults->module : 'default' ;
                $page = isset($r->defaults->page) ? 1 : null ;
                switch ($key) {
                    case 'o2feed':
                        if ($this->routeProperties[0] == 'pl' || $this->routeProperties[0] == 'in') {
                            $this->route->addRoute(
                                "langmod_$key",
                                new Zend_Controller_Router_Route(
                                    '/:lang/'.$r->route,
                                    array(
                                        'lang' => ':lang',
                                        'action' => 'top10.xml',
                                        'controller' => 'o2feed'
                                    )
                                )
                            );
                        }
                        break;
                    default:
                        $this->route->addRoute(
                            "langmod_$key",
                            new Zend_Controller_Router_Route(
                                '/:lang/'.$r->route,
                                array(
                                    'lang' => ':lang',
                                    'action' => $r->defaults->action,
                                    'controller' => $r->defaults->controller,
                                    'module' => $module,
                                    'page' => $page
                                )
                            )
                        );
                        break;
                }
            } else {
                $routeLanguage = new Zend_Controller_Router_Route(':lang', array('lang' => ':lang'));
                $baseChain = new Zend_Controller_Router_Route(
                    '@redactie',
                    array(
                        'controller' => 'about',
                        'module' => 'default'
                    )
                );
                switch ($key) {
                    case 'profilepage':
                        $page = new Zend_Controller_Router_Route_Regex(
                            '^(\d?+)$',
                            array(
                                'page' => '1',
                                'action' => 'index'),
                            array(
                                1 => 'page'
                            ),
                            '%d'
                        );
                        $chainedRoute = new Zend_Controller_Router_Route_Chain();
                        $pageChained = $chainedRoute->chain($routeLanguage)
                            ->chain($baseChain)
                            ->chain($page);
                        # add routes to router
                        $this->route->addRoute('redactier_page', $pageChained);
                        break;
                    case 'aboutdefault':
                        # validate slug parameter with regex i.e name of redactie
                        $slug = new Zend_Controller_Router_Route_Regex(
                            '^([a-zA-Z]+(?:-[a-zA-Z]+)?+)+$',
                            array( 'slug' => '',
                                'action' => 'profile'
                            ),
                            array( 1 => 'slug' ),
                            '%d'
                        );
                        # cretae slug route chain
                        $chainedRouteSlug = new Zend_Controller_Router_Route_Chain();
                        $slugChained = $chainedRouteSlug->chain($routeLanguage)
                            ->chain($baseChain)
                            ->chain($slug);
                        # add routes to router
                        $this->route->addRoute('redactier_slug', $slugChained);
                        break;
                }

            }
        }

        return ;
    }

    public function errorRouteForFlipit()
    {
        $this->route->addRoute(
            "marktplaatsfeed",
            new Zend_Controller_Router_Route(
                'marktplaatsfeed',
                array(
                    'action' => "error",
                    'controller' => "error"
                )
            )
        );
        $this->route->addRoute(
            "metronieuws",
            new Zend_Controller_Router_Route(
                'metronieuws/top10.xml',
                array(
                    'action' => "error",
                    'controller' => "error"
                )
            )
        );
        $this->route->addRoute(
            "sargassofeed",
            new Zend_Controller_Router_Route(
                'sargassofeed',
                array(
                    'action' => "error",
                    'controller' => "error"
                )
            )
        );

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
