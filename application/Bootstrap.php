<?php
require_once 'BootstrapConstant.php';

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected $moduleDirectoryName = null;
    protected $moduleName = array();
    protected $request = null;
    protected $httpHost = null;
    protected $siteName = "kortingscode.nl";
    protected $route = '';
    protected $routeProperties = '';
    protected $cdnUrl = '';
    public $frontController = '';

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
            $this->siteName = $_COOKIE['site_name'];
        }
    }

    protected function _initControllerHelpers()
    {
        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH .'/controllers/helpers');
    }

    protected function _initSiteModules()
    {
        $viewRenderer =
            Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
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
        self::constantForCacheDirectory();
        self::httpPathConstantForCdn();
        self::s3ConstantDefines();

        defined('BASE_ROOT') || define('BASE_ROOT', dirname($_SERVER['SCRIPT_FILENAME']) . '/');

        if (strlen(strtolower($this->moduleDirectoryName))==2 && $this->httpHost != "www.kortingscode.nl") {
            self::constantsForLocale();
        } elseif (trim(strtolower($this->moduleDirectoryName)) == 'admin') {
            self::constantsForAdminModule();
        } else {
            self::constantsForDefaultModule();
        }
        self::constantsForFacebookImageAndLocale();
    }

    public function constantsForFacebookImageAndLocale()
    {
        if (LOCALE == '') {
            define('FACEBOOK_IMAGE', HTTP_PATH."public/images/logo_og.png");
            define('FACEBOOK_LOCALE', '');
        } else {
            define('FACEBOOK_IMAGE', HTTP_PATH."public/images/flipit.png");
            define('FACEBOOK_LOCALE', LOCALE);
        }
    }

    public function constantForCacheDirectory()
    {
        define('HTTP_PATH', trim('http://' . HTTP_HOST . '/'));
        if (APPLICATION_ENV == 'testing') {
            define('CACHE_DIRECTORY_PATH', $this->getOption['CACHE_DIRECTORY_PATH']);
        } else {
            define('CACHE_DIRECTORY_PATH', './tmp/');
        }
    }

    public function httpPathConstantForCdn()
    {
        $this->cdnUrl = $this->getOption('cdn');
        if (isset($this->cdnUrl) && isset($this->cdnUrl[HTTP_HOST])) {
            define('HTTP_PATH_CDN', trim('http://'. $this->cdnUrl[HTTP_HOST] . '/'));
        } else {
            define('HTTP_PATH_CDN', trim('http://' . HTTP_HOST . '/'));
        }
    }

    public function s3ConstantDefines()
    {
        $s3Credentials = $this->getOption('s3');
        define('S3BUCKET', $s3Credentials['bucket']);
        define('S3KEY', $s3Credentials['key']);
        define('S3SECRET', $s3Credentials['secret']);
    }

    public function constantsForLocale()
    {
        define('LOCALE', trim(strtolower($this->moduleDirectoryName)));
        define(
            'HTTP_PATH_LOCALE',
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
                'PUBLIC_PATH_CDN',
                trim(
                    'http://'. $this->cdnUrl[HTTP_HOST]
                    .'/'. strtolower($this->moduleDirectoryName) .'/'
                )
            );
        } else {
            define(
                'PUBLIC_PATH_CDN',
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
        self::constantsImagesForLocale();
    }

    public function constantsImagesForLocale()
    {
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
            APPLICATION_PATH. '/../data/' .strtolower($this->moduleDirectoryName) .'/'. 'excels/'
        );

        defined('IMG_PATH')
        || define('IMG_PATH', PUBLIC_PATH . 'images/');
    }

    public function constantsForAdminModule()
    {
        $localeAbbreviation = '';

        if (isset($_COOKIE['locale']) && ($_COOKIE['locale']) != 'en') {
            $localeAbbreviation = $_COOKIE['locale'] . '/';
            define('LOCALE', trim($localeAbbreviation, '/'));

        } else {
            define('LOCALE', '');
        }

        if (!defined('HTTP_PATH_FRONTEND')) {
            define('HTTP_PATH_FRONTEND', trim('http://www.' . $this->siteName .'/'));
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
            . dirname($_SERVER['SCRIPT_NAME']) . '/' . $localeAbbreviation
        );

        defined('ROOT_PATH')
        || define(
            'ROOT_PATH',
            dirname($_SERVER['SCRIPT_FILENAME']) . '/' . $localeAbbreviation
        );
        self::constantsImagesForAdminModule($localeAbbreviation);
        self::constantsCdnForAdminModule();
    }

    public function constantsImagesForAdminModule($localeAbbreviation)
    {
        defined('UPLOAD_PATH')
        || define('UPLOAD_PATH', 'images/');

        defined('UPLOAD_PATH1')
        || define('UPLOAD_PATH1', $localeAbbreviation);

        defined('UPLOAD_IMG_PATH')
        || define('UPLOAD_IMG_PATH', UPLOAD_PATH . 'upload/');

        defined('UPLOAD_EXCEL_PATH')
        || define(
            'UPLOAD_EXCEL_PATH',
            APPLICATION_PATH. '/../data/' . strtolower($localeAbbreviation) . 'excels/'
        );

        defined('IMG_PATH')
        || define('IMG_PATH', PUBLIC_PATH . 'images/');

        defined('HTTP_PATH_LOCALE')
        || define(
            'HTTP_PATH_LOCALE',
            'http://' . HTTP_HOST
            . dirname($_SERVER['SCRIPT_NAME']) . '/'. strtolower($this->moduleDirectoryName) .'/'
        );
    }

    public function constantsCdnForAdminModule()
    {
        $localePath = LOCALE =='' ? '/' : '/'. strtolower(LOCALE) .'/';
        if (isset($this->cdnUrl) && isset($this->cdnUrl[HTTP_HOST])) {
            define(
                'PUBLIC_PATH_CDN',
                trim('http://'. $this->cdnUrl[HTTP_HOST] .$localePath)
            );
        } else {
            define(
                'PUBLIC_PATH_CDN',
                trim('http://' . HTTP_HOST . $localePath)
            );
        }
    }
    
    public function constantsForDefaultModule()
    {
        define('LOCALE', '');
        define('HTTP_PATH_LOCALE', trim('http://' . HTTP_HOST . '/'));
        defined('PUBLIC_PATH')
        || define(
            'PUBLIC_PATH',
            'http://' . HTTP_HOST
            . dirname($_SERVER['SCRIPT_NAME']) . '/'
        );

        if (isset($this->cdnUrl) && isset($this->cdnUrl[HTTP_HOST])) {
            define('PUBLIC_PATH_CDN', trim('http://'. $this->cdnUrl[HTTP_HOST] . '/'));
        } else {
            define('PUBLIC_PATH_CDN', trim('http://' . HTTP_HOST . '/'));
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
        || define('IMG_PATH', PUBLIC_PATH . 'images/');
    }

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
            $doctrineOptions['imbull'],
            'doctrine'
        );

        $localSiteDbConnection =
            Doctrine_Manager::connection(
                $doctrineOptions[strtolower(self::getLocaleNameForDbConnection())]['dsn'],
                'doctrine_site'
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

    public function getTranslationSettings()
    {
        # add suffix according to locale
        $suffix = "" ;
        if (LOCALE) {
            $suffix = "_" . strtoupper(LOCALE);
        }

        $domain = $_SERVER['HTTP_HOST'];

        if (strlen($this->moduleDirectoryName) == 2) {
            if ($domain != "www.kortingscode.nl" && $domain != "kortingscode.nl") {
                $localePath = '/'.$this->moduleDirectoryName.'/' ;
            } else {
                $localePath = '/' ;
            }
        } elseif ($this->moduleDirectoryName == 'admin') {

            $localePath =  isset($_COOKIE['locale']) && $_COOKIE['locale'] != 'en' ? '/'.$_COOKIE['locale'].'/' : '/'  ;
        } else {
            $localePath = '/' ;
        }

        $locale = Signupmaxaccount::getAllMaxAccounts();
        $locale = !empty($locale[0]['locale']) ? $locale[0]['locale'] : 'nl_NL';

        return array('locale' => $locale, 'localePath' => $localePath, 'suffix' => $suffix);
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
        $transSettings = $this->getTranslationSettings();
        $locale = $transSettings['locale'];

        // INFO
        // in every module folder there needs to be a "language/$locale" dir with translations.csv with minimal content "","" present.
        // The admin needs a link to do translations for a store, we need to add this link and activate a session value to activate the translator lib.
        // Replace the current Translation helper for the new T helper
        // UI edits

        Zend_Registry::set('Zend_Locale', $locale);
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

    protected function _initPluginLiveTranslation()
    {
        $transSettings  = $this->getTranslationSettings();
        $locale         = $transSettings['locale'];
        $localePath     = $transSettings['localePath'];
        $suffix         = $transSettings['suffix'];

        if ($this->moduleDirectoryName != 'admin') {
            $session        = new Zend_Session_Namespace('Transl8');
            $activationMode = (isset($session->onlineTranslationActivated))
            ? $session->onlineTranslationActivated
            : false;
        } else {
            $activationMode = false;
        }

        Zend_Registry::set('Transl8_Activated', $activationMode);
        Transl8_Translate_Writer_Csv::setDestinationFolder(
            APPLICATION_PATH.'/../public'.$localePath.'language'
        );
        
        if (Zend_Registry::get('Transl8_Activated')) {
            $plugin = new Transl8_Controller_Plugin_Transl8();
            $plugin->setActionGetFormData($localePath.'trans/getformdata');
            $plugin->setActionSubmit($localePath.'trans/submit');
            $front = Zend_Controller_Front::getInstance();
            $front->registerPlugin($plugin);

            Zend_Controller_Action_HelperBroker::addPath(
                APPLICATION_PATH . '/../library/Transl8/Controller/Action/Helper/',
                'Transl8_Controller_Action_Helper'
            );

            $locales[Zend_Registry::get('Zend_Locale')] = Zend_Registry::get('Zend_Locale');
            Transl8_Form::setLocales($locales);
        }
    }

    protected function _initI18n()
    {
        $transSettings  = $this->getTranslationSettings();
        $locale         = $transSettings['locale'];
        $localePath     = $transSettings['localePath'];
        $suffix         = $transSettings['suffix'];
        Zend_Locale::setDefault('en_US');
        $locale                     = new Zend_Locale(Zend_Registry::get('Zend_Locale'));

        $poTrans = new Zend_Translate(
            array(
                'adapter' => 'gettext',
                'locale'  => $locale,
                'disableNotices' => true
            )
        );

        $poTrans->addTranslation(
            array(
                'content' => APPLICATION_PATH.'/../public'.strtolower($localePath)
                .'language/fallback/frontend_php' . $suffix . '.mo',
                'locale' => $locale
            )
        );
        $poTrans->addTranslation(
            array(
                'content' => APPLICATION_PATH.'/../public'.strtolower($localePath)
                .'language/backend_php' . $suffix . '.mo',
                'locale' => $locale
            )
        );

        $translateSession = new Zend_Session_Namespace('Transl8');
        if (!empty($translateSession->onlineTranslationActivated)) {
            $dbTranslations = self::getDbTranslations($locale);
            $poTrans->addTranslation($dbTranslations);
        } else {
            $csvTranslate = self::getCsvTranslations($locale);
            $poTrans->addTranslation($csvTranslate);
        }

        $poTrans->addTranslation(
            array(
                    'content' => APPLICATION_PATH.'/../public'.strtolower($localePath)
                    .'language/email' . $suffix . '.mo',
                    'locale' => $locale
            )
        );

        $poTrans->addTranslation(
            array(
                    'content'   => APPLICATION_PATH.'/../public'.strtolower($localePath)
                    .'language/po_links' . $suffix . '.mo',
                    'locale'    => $locale
            )
        );

        Zend_Registry::set('Zend_Locale', $locale);
        Zend_Registry::set('Zend_Translate', $poTrans);
    }

    public function getDbTranslations($locale)
    {
        $getDbTranslationsForZendTranslate = Translations::getDbTranslationsForZendTranslate();
        
        $dbTranslations = new Zend_Translate(
            array(
                'adapter' => 'array',
                'locale'  => $locale,
                'disableNotices' => true
            )
        );
        $dbTranslations->addTranslation(
            array(
                    'content' => $getDbTranslationsForZendTranslate,
                    'locale' => $locale
            )
        );

        return $dbTranslations;
    }

    public function getCsvTranslations($locale)
    {
        $inlineTranslationFolder = Transl8_Translate_Writer_Csv::getDestinationFolder();
        $csvTranslation = array(
            'adapter'   => 'Transl8_Translate_Adapter_Csv',
            'scan'      => Zend_Translate::LOCALE_DIRECTORY,
            'content'   => $inlineTranslationFolder . '/',
            'locale'    => $locale
        );

        $csvTranslate = new Zend_Translate($csvTranslation);
        return $csvTranslate;
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
        $permalink = self::getPermalink();
        $this->routeProperties = explode('/', $permalink);
        $permalink = self::splitRouteProperties($permalink);
        $permalink = self::replacePermalinkString($permalink);
        // get last word in permalink using regex match
        preg_match('/[^\/]+$/', $permalink, $matches);
        // if url match with database permalink then get permalink from database and add in zend route
        // if not then check url exist in redirect then make a 301 redirect
        if (intval(@$matches[0]) > 0) {
            $permalink = explode('/'.$matches[0], $permalink);
            $getPermalinkFromDb = RoutePermalink::getRoute($permalink[0]);
            $actualPermalink = $permalink[0];
        } else {
            $getPermalinkFromDb = RoutePermalink::getRoute($permalink);
            $actualPermalink = $permalink;
        }

        // check if permalink exists in route permalink table
        if (count($getPermalinkFromDb) > 0) {
            self::setRouteForPermalink($getPermalinkFromDb, $actualPermalink);
        }
        // for 301 redirections of old indexed pages
        self::redirectionForOldWebsiteUrls($permalink);

        if ($this->routeProperties[0] != 'admin' &&
            in_array(strtolower($this->routeProperties[0]), $this->moduleName)) {
                self::setRouteForLocale();
        } else {
             // trigger error for flipt.com
            if (HTTP_HOST == 'www.flipit.com') {
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
        $permalinkWithoutLeftSlash = ltrim(REQUEST_URI, '/');
        $permalink = rtrim($permalinkWithoutLeftSlash, '/');
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
            $redirectUrl = $routeRedirectName[0]['redirectto'];
            header('Location: '.$redirectUrl.$splitPermalinkFromQueryString, true, 301);
            exit();
        }
    }

    public function splitRouteProperties($permalink)
    {
        if (count($this->routeProperties) == 1) {
            $permalink = $this->routeProperties[0];
        } elseif (count($this->routeProperties) == 2) {
            if (intval($this->routeProperties[0]) > 0) {
                $permalink = $this->routeProperties[0];
            } else {
                preg_match('/^[1-3]{1}$/', $this->routeProperties[1], $maximumIntegerNumber);
                if ($maximumIntegerNumber) {
                    $permalink = $this->routeProperties[0];
                } else {
                    $permalink = $this->routeProperties[1];
                }
            }
        } elseif (count($this->routeProperties) == 3) {
            preg_match('/^[1-3]{1}$/', $this->routeProperties[2], $maximumIntegerNumber);
            if ($maximumIntegerNumber) {
                $permalink = $this->routeProperties[2];
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

    public function setRouteForPermalink($getPermalinkFromDb, $actualPermalink)
    {
        // get the page detail from page table on the basis of permalink
        $pageDetail = RoutePermalink::getPageProperties(
            strtolower($getPermalinkFromDb[0]['permalink'])
        );
        $this->pageDetail = $pageDetail;
        //check if there exist page belongs to the permalink then append the
        //id of that page with actual URL
        if (!empty($pageDetail)) {
            $getPermalinkFromDb[0]['exactlink'] =
            $getPermalinkFromDb[0]['exactlink'].'/attachedpage/'.$this->pageDetail[0]['id'];
        }
        $permalinkUrl = explode('/', $getPermalinkFromDb[0]['exactlink']);
        $urlArray = array(
                'controller' => @$permalinkUrl[0],
                'action'     => @$permalinkUrl[1]
        );
        for ($index = 2; $index < count(@$permalinkUrl); $index++) {
            if ($index % 2 == 0) {
                $urlArray[@$permalinkUrl[$index]] = @$permalinkUrl[$index+1];
            }
        }
        if (!empty($pageDetail)) {
            $urlArray['attachedpage'] = $this->pageDetail[0]['id'];
        }
        if (in_array(strtolower($this->routeProperties[0]), $this->moduleName)) {

            $urlArray['module'] = 'default';
            $urlArray['lang'] = $this->routeProperties[0];

            self::routeForDefaultModule();

            $route = new Zend_Controller_Router_Route(
                $this->routeProperties[0] .'/'. $actualPermalink,
                $urlArray
            );
            $this->route->addRoute('user', $route);

            return;
        } else {
            $route = new Zend_Controller_Router_Route($actualPermalink, $urlArray);
            $this->route->addRoute('user', $route);
        }

        return;
    }

    public function routeForDefaultModule()
    {
        if (HTTP_HOST == 'www.kortingscode.nl') {
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
            return;
        }
    }

    public function redirectionForOldWebsiteUrls($permalink)
    {
        // for 301 redirections of old indexed pages
        if (is_array($permalink)) {
            $permalinkType = explode('/', $permalink[0]);
        } else {
            $permalinkType = explode('/', $permalink);
        }

        switch ($permalinkType[0]) {
            case 'kortingen':
                $newPermalink = HTTP_PATH.'nieuw';
                header('Location: '.$newPermalink, true, 301);
                die();
                break;
            case 'shops':
                $newPermalink = HTTP_PATH.'store';
                header('Location: '.$newPermalink, true, 301);
                die();
                break;
            case 'producten':
                $newPermalink = HTTP_PATH.'categorieen';
                header('Location: '.$newPermalink, true, 301);
                die();
                break;
            case 'rssfeeds':
            case 'get-action-ratings':
            case 'get-shop-rating':
            case 'get-shop-reviews':
            case 'get-shop-ratings':
            case 'dynamics':
            case '2010':
            case '2011':
            case '2012':
                $newPermalink = HTTP_PATH;
                header('Location: '.$newPermalink, true, 301);
                die();
                break;
            default:
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
                        $module  = isset($r->defaults->module) ? $r->defaults->module : 'default';
                        $page = isset($r->defaults->page) ? 1 : null;
                        $this->route->addRoute(
                            'langmod_'. $key,
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
            if ($r->type != 'Zend_Controller_Router_Route_Regex') {
                $module  = isset($r->defaults->module) ? $r->defaults->module : 'default';
                $page = isset($r->defaults->page) ? 1 : null;
                switch ($key) {
                    case 'o2feed':
                        if ($this->routeProperties[0] == 'pl' || $this->routeProperties[0] == 'in') {
                            $this->route->addRoute(
                                'langmod_'. $key,
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
                            'langmod_'. $key,
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
                        // add routes to router
                        $this->route->addRoute('redactier_page', $pageChained);
                        break;
                    case 'aboutdefault':
                        // validate slug parameter with regex i.e name of redactie
                        $slug = new Zend_Controller_Router_Route_Regex(
                            '^([a-zA-Z]+(?:-[a-zA-Z]+)?+)+$',
                            array( 'slug' => '',
                                'action' => 'profile'
                            ),
                            array( 1 => 'slug' ),
                            '%d'
                        );
                        // cretae slug route chain
                        $chainedRouteSlug = new Zend_Controller_Router_Route_Chain();
                        $slugChained = $chainedRouteSlug->chain($routeLanguage)
                            ->chain($baseChain)
                            ->chain($slug);
                        // add routes to router
                        $this->route->addRoute('redactier_slug', $slugChained);
                        break;
                }

            }
        }
        return;
    }

    public function errorRouteForFlipit()
    {
        $this->route->addRoute(
            'marktplaatsfeed',
            new Zend_Controller_Router_Route(
                'marktplaatsfeed',
                array(
                    'action' => "error",
                    'controller' => "error"
                )
            )
        );
        $this->route->addRoute(
            'metronieuws',
            new Zend_Controller_Router_Route(
                'metronieuws/top10.xml',
                array(
                    'action' => "error",
                    'controller' => "error"
                )
            )
        );
        $this->route->addRoute(
            'sargassofeed',
            new Zend_Controller_Router_Route(
                'sargassofeed',
                array(
                    'action' => "error",
                    'controller' => "error"
                )
            )
        );
        $this->route->addRoute(
            'whitelabel',
            new Zend_Controller_Router_Route(
                'whitelabel/top10.xml',
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
