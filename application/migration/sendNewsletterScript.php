<?php
/**
 * Send scheduled newsletter of all countries
 *
 * @author Sp Singh
 *
 */
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
        require_once(LIBRARY_PATH.'/PHPExcel/PHPExcel.php');
        require_once(LIBRARY_PATH.'/BackEnd/Helper/viewHelper.php');
        require_once (LIBRARY_PATH . '/Zend/Application.php');
        require_once(DOCTRINE_PATH . '/Doctrine.php');

        // Create application, bootstrap, and run
        $application = new Zend_Application(APPLICATION_ENV,
                APPLICATION_PATH . '/configs/application.ini');

        $frontControlerObject = $application->getOption('resources');


        $this->_mandrillKey = $frontControlerObject['frontController']['params']['mandrillKey'];
        $this->_template = $frontControlerObject['frontController']['params']['newsletterTemplate'];

        $connections = $application->getOption('doctrine');
        spl_autoload_register(array('Doctrine', 'autoload'));

        $manager = Doctrine_Manager::getInstance();

        $imbull = $connections['imbull'];

        // cycle htoruh all site database
        $DMC1 = Doctrine_Manager::connection($connections['imbull'], 'doctrine');


        // cycle thoruh all site database
        foreach ( $connections as $key => $connection ) {
            // check database is being must be site
            if ($key != 'imbull') {
                try {
                    $this->send( $connection ['dsn'], $key ,$imbull );
                } catch ( Exception $e ) {
                    echo $e->getMessage ();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }

        $manager->closeConnection($DMC1);
    }

    protected function send($dsn, $key,$imbull)
    {
        # setup appropriate vaues according to locale
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
            || define('PUBLIC_PATH',
                dirname(dirname(dirname(__FILE__)))."/public/");

        $DMC = Doctrine_Manager::connection($dsn, 'doctrine_site');
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));

        $manager = Doctrine_Manager::getInstance();
        //Doctrine_Core::loadModels(APPLICATION_PATH . '/models/generated');

        $manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
        $manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        Doctrine_Core::loadModels(APPLICATION_PATH . '/models');

        try {

            $settings = Signupmaxaccount::getAllMaxAccounts();

            # check if newsletter is scheduled and still not sent then proceed with  newsletter sending
            if($settings[0]['newletter_is_scheduled'] && $settings[0]['newletter_status'] ==  0) {
                $cutsomLocale = !empty( $settings[0]['locale']) ? $settings[0]['locale'] : 'nl_NL';


                $this->_trans = new Zend_Translate(array(
                        'adapter' => 'gettext',
                        'disableNotices' => true));

                $this->_trans->addTranslation(
                        array(
                                'content' => APPLICATION_PATH.'/../public/'. strtolower($this->_localePath).'language/frontend_php' . $suffix . '.mo',
                                'locale' => $cutsomLocale,
                        )
                );

                $this->_trans->addTranslation(
                        array(
                                'content' => APPLICATION_PATH.'/../public/'.strtolower($this->_localePath).'language/po_links' . $suffix . '.mo',
                                'locale' => $cutsomLocale ,
                        )
                );



                Zend_Registry::set('Zend_Translate', $this->_trans);
                Zend_Registry::set('Zend_Locale', $cutsomLocale);

                $timezone = $settings[0]['timezone'];

                echo "\n" ;

                $sentTime = new Zend_Date($settings[0]['newletter_scheduled_time']);
                $sentTime->get('YYYY-MM-dd HH:mm:ss');

                $currentTime = new Zend_Date();
                $currentTime->setTimezone($timezone);

                echo "\n" ;
                $currentTime->get('YYYY-MM-dd HH:mm:ss');

                # if sent time is passed then send newsletter
                if ($currentTime->isLater($sentTime)) {

                    echo "\nSending newletter...\n" ;

                    $this->mandrilHandler($key, $settings);
                }

            } else {

                echo "\n";
                print "$key - Already sent";

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
        $topCategories = array_slice(FrontEnd_Helper_viewHelper::gethomeSections("category", 10), 0, 1);
        $topVouchercodes = Offer::getTopOffers(10);
        $categoryVouchers = array_slice(Category::getCategoryVoucherCodes($topCategories[0]['categoryId']), 0, 3);
        BackEnd_Helper_MandrillHelper::getDirectLoginLinks($this, 'script', '', $this->_mandrillKey);
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
                $settings[0]['email_footer'],
                array(
                    'httpPath' => $this->_hostName,
                    'locale' => $key,
                    'httpPathLocale' => $this->_linkPath,
                    'publicPathCdn' => $this->_public_cdn_path,
                    'mandrillKey' => $this->_mandrillKey
                )
            );
            Signupmaxaccount::updateNewsletterSchedulingStatus();
            $message = 'Newsletter has been sent successfully' ;
        } catch (Mandrill_Error $e) {
            $message ='There is some problem in your data';
        }
        echo "\n";
        print "$key - $message ";
    }
}

new SendNewsletter();
