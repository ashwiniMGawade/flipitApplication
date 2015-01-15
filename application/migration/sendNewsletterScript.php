<?php

class SendNewsletter
{
    public $_localePath = '/';
    public $_hostName = '';
    public $_trans = null;
    public $_locale = '';
    public $_siteName = null;
    public $_logo = null;
    public $_linkPath = null;
    public $_publicPath = null;
    public $_public_cdn_path = null;
    public $_http_path_cdn = null;
    public $_recipientMetaData  = array();
    public $_loginLinkAndData = array();
    public $_to = array();
    public $_mandrillKey = "";
    public $_template = "" ;
    public $_rootPath = "" ;

    public function __construct()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        defined('APPLICATION_PATH')
            || define(
                'APPLICATION_PATH',
                dirname(dirname(__FILE__))
            );
        defined('LIBRARY_PATH')
            || define('LIBRARY_PATH', realpath(dirname(dirname(dirname(__FILE__))). '/library'));
        defined('DOCTRINE_PATH') || define('DOCTRINE_PATH', LIBRARY_PATH . '/Doctrine');
        defined('APPLICATION_ENV')
        || define(
            'APPLICATION_ENV',
            (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV')
                : 'production')
        );
        set_include_path(
            implode(
                PATH_SEPARATOR,
                array(
                    realpath(APPLICATION_PATH . '/../library'),
                    get_include_path(),)
            )
        );
        set_include_path(
            implode(
                PATH_SEPARATOR,
                array(realpath(DOCTRINE_PATH), get_include_path(),)
            )
        );

        require_once(LIBRARY_PATH.'/PHPExcel/PHPExcel.php');
        require_once(LIBRARY_PATH.'/BackEnd/Helper/viewHelper.php');
        require_once (LIBRARY_PATH . '/Zend/Application.php');
        require_once(DOCTRINE_PATH . '/Doctrine.php');
        $application = new Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );
        $frontControlerObject = $application->getOption('resources');
        $this->_mandrillKey = $frontControlerObject['frontController']['params']['mandrillKey'];
        $this->_template = $frontControlerObject['frontController']['params']['newsletterTemplate'];
        $connections = $application->getOption('doctrine');
        spl_autoload_register(array('Doctrine', 'autoload'));
        $manager = Doctrine_Manager::getInstance();
        $imbull = $connections['imbull'];
        $DMC1 = Doctrine_Manager::connection($connections['imbull'], 'doctrine');

        foreach ($connections as $key => $connection) {
            if ($key != 'imbull') {
                try {
                    $this->send($connection ['dsn'], $key, $imbull);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }
        $manager->closeConnection($DMC1);
    }

    protected function send($dsn, $key, $imbull)
    {
        if ($key == 'en') {
            $this->_localePath = '';
            $this->_hostName = "http://www.kortingscode.nl";
            $this->_logo = $this->_hostName ."/public/images/HeaderMail.gif" ;
            $suffix = "" ;
            $this->_locale = '';
            $this->_siteName = "Kortingscode.nl";
            $this->_public_cdn_path = "http://img.kortingscode.nl/public/";
        } else {
            $this->_localePath = $key . "/";
            $this->_hostName = "http://www.flipit.com";
            $suffix = "_" . strtoupper($key) ;
            $this->_locale = $key;
            $this->_siteName = "Flipit.com";
            $this->_logo =  $this->_hostName ."/public/images/flipit-welcome-mail.jpg";
            $this->_public_cdn_path = "http://img.flipit.com/public/" . strtolower($this->_localePath);
        }

        defined('PUBLIC_PATH')
            || define(
                'PUBLIC_PATH',
                dirname(dirname(dirname(__FILE__)))."/public/"
            );

        //code for cache intialization
        defined('LOCALE') || define('LOCALE', $key);
        $frontendOptions = array(
           'lifetime' => 300,
           'automatic_serialization' => true
        );
        defined('CACHE_DIRECTORY_PATH') || define('CACHE_DIRECTORY_PATH', PUBLIC_PATH.'tmp/');
        $backendOptions = array('cache_dir' => CACHE_DIRECTORY_PATH);
        $cache = Zend_Cache::factory(
            'Output',
            'File',
            $frontendOptions,
            $backendOptions
        );
        Zend_Registry::set('cache', $cache);

        $DMC = Doctrine_Manager::connection($dsn, 'doctrine_site');
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));
        $manager = Doctrine_Manager::getInstance();
        $manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
        $manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        Doctrine_Core::loadModels(APPLICATION_PATH . '/models');

        try {
            $settings = Signupmaxaccount::getAllMaxAccounts();
            $localeSettings = LocaleSettings::getLocaleSettings();
            $currentDate = FrontEnd_Helper_viewHelper::getCurrentDate();
            if (($settings[0]['newsletter_sent_time'] != $currentDate || $settings[0]['newsletter_sent_time'] == '')
             && $settings[0]['newletter_status'] !=  1) {
                if ($settings[0]['newletter_is_scheduled'] && $settings[0]['newletter_status'] ==  0) {
                    $cutsomLocale = !empty( $localeSettings[0]['locale']) ? $localeSettings[0]['locale'] : 'nl_NL';
                    $this->_trans = new Zend_Translate(array(
                            'adapter' => 'gettext',
                            'disableNotices' => true));
                    $this->_trans->addTranslation(
                        array(
                            'content' => APPLICATION_PATH.'/../public/'. strtolower($this->_localePath).
                            'language/frontend_php' . $suffix . '.mo',
                            'locale' => $cutsomLocale,
                        )
                    );
                    $this->_trans->addTranslation(
                        array(
                            'content' => APPLICATION_PATH.'/../public/'. strtolower($this->_localePath).
                            'language/email' . $suffix . '.mo',
                            'locale' => $cutsomLocale
                        )
                    );
                    $this->_trans->addTranslation(
                        array(
                            'content' => APPLICATION_PATH.'/../public/'. strtolower($this->_localePath).
                            'language/form' . $suffix . '.mo',
                            'locale' => $cutsomLocale
                        )
                    );
                    $this->_trans->addTranslation(
                        array(
                            'content'   => APPLICATION_PATH.'/../public/'. strtolower($this->_localePath).
                            'language/po_links' . $suffix . '.mo',
                            'locale'    => $cutsomLocale
                        )
                    );
                    Zend_Registry::set('Zend_Translate', $this->_trans);
                    Zend_Registry::set('Zend_Locale', $cutsomLocale);
                    $timezone = $localeSettings[0]['timezone'];
                    echo "\n" ;
                    $sentTime = new Zend_Date($settings[0]['newletter_scheduled_time']);
                    $sentTime->get('YYYY-MM-dd HH:mm:ss');
                    $currentTime = new Zend_Date();
                    $currentTime->setTimezone($timezone);
                    echo "\n" ;
                    $currentTime->get('YYYY-MM-dd HH:mm:ss');
                    if ($currentTime->isLater($sentTime)) {
                        echo "\nSending newletter...\n" ;
                        $this->mandrilHandler($key, $settings);
                    }
                } else {
                    echo "\n";
                    print "$key - Already sent";
                }
            } else {
                    echo "\n";
                    print "$key - Newsletter has already been sent for the same day.";
            }
        } catch (Exception $e) {
            echo "\n";
            echo $e->getMessage();
        }

        $manager->closeConnection($DMC);
    }


    protected function mandrilHandler($key, $settings)
    {
        $this->_linkPath = $this->_hostName . '/' .$this->_localePath;
        $this->_publicPath = $this->_hostName . '/public/' . $this->_localePath;
        $this->_rootPath = PUBLIC_PATH . $this->_localePath;
        //set/get cache for news letter
        $newsLetterHeaderFooter = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'newsletter_emailHeader_emailFooter',
            array('function' => 'Signupmaxaccount::getEmailHeaderFooter', 'parameters' => array()),
            ''
        );
    
        $topCategories = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'newsletter_top_categories',
            array(
                'function' => 'FrontEnd_Helper_viewHelper::gethomeSections',
                'parameters' => array('category', 10)),
            ''
        );

        $topCategories = array_slice($topCategories, 0, 1);

        $topVouchercodes = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'newsletter_top_offers',
            array(
                'function' => 'Offer::getTopOffers',
                'parameters' => array(10)),
            ''
        );

        $categoryVouchers = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'newsletter_category_vouchercodes',
            array(
                'function' => 'Category::getCategoryVoucherCodes',
                'parameters' => array($topCategories[0]['categoryId'])),
            ''
        );

        $categoryVouchers = array_slice($categoryVouchers, 0, 3);

        BackEnd_Helper_MandrillHelper::getDirectLoginLinks($this, 'scheduleNewsletterSender', '', $this->_mandrillKey);
        $mandrill = new Mandrill_Init($this->_mandrillKey);
        $mandrillSenderEmailAddress = $settings[0]['emailperlocale'];
        $mandrillNewsletterSubject = $settings[0]['emailsubject'];
        $mandrillSenderName = $settings[0]['sendername'];
        $categoryName = $topCategories[0]['category']['name'];
        $categoryPermalink = $topCategories[0]['category']['permaLink'];
        try {
            FrontEnd_Helper_viewHelper::sendMandrillNewsletterByBatch(
                $topVouchercodes,
                $categoryVouchers,
                $categoryName.'|'.$categoryPermalink,
                $mandrillNewsletterSubject,
                $mandrillSenderEmailAddress,
                $mandrillSenderName,
                $this->_recipientMetaData,
                $this->_loginLinkAndData,
                $this->_to,
                $newsLetterHeaderFooter['email_footer'],
                array(
                    'httpPath' => $this->_hostName,
                    'locale' => $this->_locale,
                    'httpPathLocale' => $this->_linkPath,
                    'publicPathCdn' => $this->_public_cdn_path,
                    'mandrillKey' => $this->_mandrillKey
                ),
                $newsLetterHeaderFooter['email_header']
            );
            Signupmaxaccount::updateNewsletterSchedulingStatus();
            $this->clearNewsLetterCache();
            $message = 'Newsletter has been sent successfully';
        } catch (Mandrill_Error $e) {
            $message ='There is some problem in your data';
        }
        echo "\n";
        print "$key - $message ";
    }

    protected function clearNewsLetterCache()
    {
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('newsletter_emailHeader_emailFooter');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('newsletter_top_categories');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('newsletter_top_offers');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('newsletter_category_vouchercodes');
    }
}

new SendNewsletter();
