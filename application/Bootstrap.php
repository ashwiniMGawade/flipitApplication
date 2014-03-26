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
    protected $_moduleDirectoryName = null;
    protected $_moduleName = array();
    protected $_request = null;
    protected $_httpHost = null;
    protected $_siteName = "kortingscode.nl" ;
    protected $_frontController = '';
    protected $_route ='';
    protected $_routeProperties = '';

    /**
     * Set base controller or view request
     *
     * @version 1.0
     */
    public function _initRequest()
    {
        $this->bootstrap('frontController');
        $this->_frontController = $this->getResource('frontController');
        $this->_frontController->setRequest(new Zend_Controller_Request_Http());
        $this->_request = $this->_frontController->getRequest();
        $this->_httpHost = $this->_request->getHttpHost();
        $this->_route  = Zend_Controller_Front::getInstance()->getRouter();
        Zend_Registry::set('request', $this->_request);
        Zend_Registry::set('db_locale', false);

        if (isset($_COOKIE['site_name'])) {
            $this->_siteName = $_COOKIE['site_name'] ;
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
        $this->_frontController->addModuleDirectory(APPLICATION_PATH . '/modules');
    }
    /**
     * set initial content according to locale settings
     *
     */
    public function _initContants()
    {
        $routeUrl = ltrim(REQUEST_URI, '/');
        $this->_routeProperties = preg_split("/[\/\?]+/", $routeUrl);
        $routeUrlWithoutSlash  = rtrim($this->_routeProperties[0], '/');
        $moduleDirectoryNames = $this->_frontController->getControllerDirectory();
        $this->_moduleName = array_keys($moduleDirectoryNames);
        
        if (in_array(strtolower($routeUrlWithoutSlash), $this->_moduleName)) {
            $this->_moduleDirectoryName = strtolower($this->_routeProperties[0]);
        } else {
            $this->_moduleDirectoryName = "default" ;
        }
        
        #Enviorment Settings
        define("HTTP_PATH", trim('http://' . HTTP_HOST . '/'));
        $environment = $this->getOption('ENV');
        if ($environment == 'dev') {
            define("CACHE_DIRECTORY_PATH", $this->getOption['CACHE_DIRECTORY_PATH']);
        } else {
            define("CACHE_DIRECTORY_PATH", './tmp/');
        }

        #CDN Settings
        $cdnUrl = $this->getOption('cdn');
        if (isset($cdnUrl) && isset($cdnUrl[HTTP_HOST])) {
            define("HTTP_PATH_CDN", trim('http://'. $cdnUrl[HTTP_HOST] . '/'));
        } else {
            define("HTTP_PATH_CDN", trim('http://' . HTTP_HOST . '/'));
        }

        defined('BASE_ROOT') || define("BASE_ROOT", dirname($_SERVER['SCRIPT_FILENAME']) . '/');

        if (strlen(strtolower($this->_moduleDirectoryName))==2 && $this->_httpHost != "www.kortingscode.nl") {
            define("LOCALE", trim(strtolower($this->_moduleDirectoryName)));
            define(
                "HTTP_PATH_LOCALE",
                trim('http://' . HTTP_HOST . '/' . $this->_moduleDirectoryName .'/')
            );
            defined('PUBLIC_PATH')
            || define(
                'PUBLIC_PATH',
                'http://' . HTTP_HOST. dirname(
                    $_SERVER['SCRIPT_NAME']
                ) . '/'.strtolower($this->_moduleDirectoryName) .'/'
            );
            
            if (isset($cdnUrl) && isset($cdnUrl[HTTP_HOST])) {
                define(
                    "PUBLIC_PATH_CDN",
                    trim(
                        'http://'. $cdnUrl[HTTP_HOST]
                        .'/'. strtolower($this->_moduleDirectoryName) .'/'
                    )
                );
            } else {
                define(
                    "PUBLIC_PATH_CDN",
                    trim(
                        'http://' . HTTP_HOST
                        . '/'. strtolower($this->_moduleDirectoryName) .'/'
                    )
                );
            }

            defined('ROOT_PATH')
            || define(
                'ROOT_PATH',
                dirname($_SERVER['SCRIPT_FILENAME']) . '/'
                . strtolower($this->_moduleDirectoryName) .'/'
            );

            defined('UPLOAD_PATH')
            || define(
                'UPLOAD_PATH',
                strtolower($this->_moduleDirectoryName) .'/'. 'images/'
            );

            defined('UPLOAD_IMG_PATH')
            || define('UPLOAD_IMG_PATH', UPLOAD_PATH . 'upload/');

            defined('UPLOAD_EXCEL_PATH')
            || define(
                'UPLOAD_EXCEL_PATH',
                APPLICATION_PATH. '/../data/' . strtolower($this->_moduleDirectoryName) .'/'. 'excels/'
            );

            defined('IMG_PATH')
            || define('IMG_PATH', PUBLIC_PATH . "images/");

        } elseif (trim(strtolower($this->_moduleDirectoryName)) == 'admin') {

            $localAbbreviation = '';

            if (isset($_COOKIE['locale']) && ($_COOKIE['locale']) != 'en') {
                $localAbbreviation = $_COOKIE['locale'] . "/";
                define("LOCALE", trim($localAbbreviation, '/'));

            } else {
                define("LOCALE", '');
            }

            if (! defined('HTTP_PATH_FRONTEND')) {
                define("HTTP_PATH_FRONTEND", trim('http://www.' . $this->_siteName ."/"));
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
                . dirname($_SERVER['SCRIPT_NAME']) . '/'. strtolower($this->_moduleDirectoryName) .'/'
            );

        } else {
            define("LOCALE", '');
            define("HTTP_PATH_LOCALE", trim('http://' . HTTP_HOST . '/'));
            defined('PUBLIC_PATH')
            || define(
                'PUBLIC_PATH',
                'http://' . HTTP_HOST
                . dirname($_SERVER['SCRIPT_NAME']) . '/'
            );

            if (isset($cdnUrl) && isset($cdnUrl[HTTP_HOST])) {
                define("PUBLIC_PATH_CDN", trim('http://'. $cdnUrl[HTTP_HOST] . '/'));
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

        if (strlen($this->_moduleDirectoryName) == 2) {
            $locale = $this->_moduleDirectoryName ;
        } elseif ($this->_moduleDirectoryName == 'admin') {
            $locale =  isset($_COOKIE['locale']) ? $_COOKIE['locale'] : 'en'  ;
        } else if($this->_moduleDirectoryName == "default") {
            $locale = 'en' ;
        }

        $localSiteDbConnection =
            Doctrine_Manager::connection(
                $doctrineOptions[strtolower($locale)]['dsn'],
                "doctrine_site"
            );
        date_default_timezone_set('Europe/Amsterdam');
        return $imbullDbConnection;
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
        $domain = HTTP_HOST;
        if (strlen($this->_moduleDirectoryName) == 2) {
            if ($domain != "www.kortingscode.nl") {
                $localePath = '/'.$this->_moduleDirectoryName.'/' ;
            } else {
                $localePath = '/' ;
            }
        } elseif ($this->_moduleDirectoryName == 'admin') {
            $localePath =  isset($_COOKIE['locale'])
            && $_COOKIE['locale'] != 'en' ? '/'.$_COOKIE['locale'].'/' : '/'  ;
        } else {
            $localePath = '/' ;
        }
        
        $localeName = Signupmaxaccount::getLocaleName();
        $languageCultureName = !empty($localeName[0]['locale']) ? $localeName[0]['locale'] : 'nl_NL';
        $zendTranslate = new Zend_Translate(
            array(
                'adapter' => 'gettext',
                'disableNotices' => true
            )
        );
        $languagePath = APPLICATION_PATH.'/../public'.strtolower($localePath).'language/';
        $zendTranslate->addTranslation(
            array(
                'content' => $languagePath.'frontend_php'
                . $localeNameForTranslateFile . '.mo',
                'locale' => $languageCultureName,
           )
        );
        $zendTranslate->addTranslation(
            array(
                'content' => $languagePath.'po_links'
                 . $localeNameForTranslateFile . '.mo',
                 'locale' => $languageCultureName,
            )
        );
        $zendTranslate->addTranslation(
            array(
                'content' => $languagePath.'backend_php'
                . $localeNameForTranslateFile. '.mo',
                'locale' => $languageCultureName,
           )
        );
        Zend_Registry::set('Zend_Translate', $zendTranslate);
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
        $permalink = ltrim(REQUEST_URI, '/');
        $domain = HTTP_HOST;
        $permalink = rtrim($permalink, '/');
        $routeRedirectName = RouteRedirect::getRoute(HTTP_PATH.$permalink);
        $splitPermalinkFromQueryString = strstr($permalink, '?');

        if (!empty($splitPermalinkFromQueryString)) {
            $permalink = strstr($permalink, '?', true);
        }
        if (count($routeRedirectName) > 0) {
            $redirectUrl =  $routeRedirectName[0]['redirectto'];
            header('Location: '.$redirectUrl.$splitPermalinkFromQueryString, true, 301);
            exit();
        }

        $this->_routeProperties =  explode('/', $permalink);

        if (count($this->_routeProperties) == 1) {
            $permalink = $this->_routeProperties[0] ;
        } elseif (count($this->_routeProperties) == 2) {
            if (intval($this->_routeProperties[0]) > 0) {
                $permalink = $this->_routeProperties[0] ;
            } else {
                 preg_match('/^[1-3]{1}$/', $this->_routeProperties[1], $mInt);
                if ($mInt) {
                    $permalink = $this->_routeProperties[0] ;
                } else {
                    $permalink = $this->_routeProperties[1] ;
                }
            }
        } elseif (count($this->_routeProperties) == 3) {
            preg_match('/^[1-3]{1}$/', $this->_routeProperties[2], $mInt);
            if ($mInt) {
                $permalink = $this->_routeProperties[2] ;
            }
        }

        $searchString = '~([a-zA-z]+.)([\?].+)~';
        $replaceString = '$1';

        preg_match($searchString, $permalink, $resultString);

        if ($resultString) {
            $permalink = preg_replace($searchString, $replaceString, $permalink);
        }

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
            if (in_array(strtolower($this->_routeProperties[0]), $this->_moduleName)) {

                $paramArray['module'] = 'default';
                $paramArray['lang'] = $this->_routeProperties[0];

                if ($domain == "kortingscode.nl" || $domain == "www.kortingscode.nl") {
                    $this->_route->addRoute(
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
        
                $route = new Zend_Controller_Router_Route(
                    $this->_routeProperties[0] .'/'. $actualPermalink .'/*',
                    $paramArray
                );
                $this->_route->addRoute('user', $route);
                return;
            } else {
                $route = new Zend_Controller_Router_Route($actualPermalink.'/*', $paramArray);
                $this->_route->addRoute('user', $route);
            }
        }
        # for 301 redirections of old indexed pages
        if (is_array($permalink)) {
            $permalinkToCheck = explode('/', $permalink[0]);
        } else {
            $permalinkToCheck = explode('/', $permalink);
        }

        switch ($permalinkToCheck[0]) {
            case "kortingen":
                $newpermalink = HTTP_PATH."nieuw";
                header("Location: ".$newpermalink, true, 301);
                die();
                break;
            case "shops":
                $newpermalink = HTTP_PATH."store";
                header("Location: ".$newpermalink, true, 301);
                die();
                break;
            case "producten":
                $newpermalink = HTTP_PATH."categorieen";
                header("Location: ".$newpermalink, true, 301);
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
                header("Location: ".$newpermalink, true, 301);
                die();
                break;
        }

        $config = new Zend_Config_Ini(APPLICATION_PATH.'/configs/routes.ini', 'production');
        if ($this->_routeProperties[0] != 'admin' &&
            in_array(strtolower($this->_routeProperties[0]), $this->_moduleName)) {
            if ($domain == "kortingscode.nl" || $domain == "www.kortingscode.nl") {
                $this->_route->addRoute(
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

            if ($this->_request->isXmlHttpRequest()) {
                $this->_route->addRoute(
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
                            $this->_route->addRoute(
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
                            if ($this->_routeProperties[0] == 'pl' || $this->_routeProperties[0] == 'in') {
                                $this->_route->addRoute(
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
                            $this->_route->addRoute(
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
                            $this->_route->addRoute('redactier_page', $pageChained);
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
                            $this->_route->addRoute('redactier_slug', $slugChained);
                            break;
                    }

                }
            }
            return ;
        } else {
             # trigger error for flipt.com
            if ($domain == "flipit.com" || $domain == "www.flipit.com") {
                $this->_route->addRoute(
                    "marktplaatsfeed",
                    new Zend_Controller_Router_Route(
                        'marktplaatsfeed',
                        array(
                            'action' => "error",
                            'controller' => "error"
                        )
                    )
                );
                $this->_route->addRoute(
                    "metronieuws",
                    new Zend_Controller_Router_Route(
                        'metronieuws/top10.xml',
                        array(
                            'action' => "error",
                            'controller' => "error"
                        )
                    )
                );
                $this->_route->addRoute(
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
            //route redirection instance for rules written in routes.ini
            $this->_route->addConfig(
                new Zend_Config_Ini(
                    APPLICATION_PATH.'/configs/routes.ini',
                    'production'
                ),
                'routes'
            );
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
