<?php

class SendCodeAlert
{
    public $localePath = '/';
    public $hostName = '';
    public $zendTranslate = '';
    public $locale = '';
    public $siteName = '';
    public $logo = '';
    public $_linkPath = '';
    public $publicCdnPath = '';
    public $_recipientMetaData  = array();
    public $_loginLinkAndData = array();
    public $_to = array();
    public $mandrillKey = "";
    public $template = "";
    public $shopId = "";

    public function __construct()
    {
        require_once 'ConstantForMigration.php';
        require_once('CommonMigrationFunctions.php');
        CommonMigrationFunctions::setTimeAndMemoryLimit();
        $application = new Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );
        $frontControlerObject = $application->getOption('resources');
        $this->mandrillKey = $frontControlerObject['frontController']['params']['mandrillKey'];
        $this->template = $frontControlerObject['frontController']['params']['newsletterTemplate'];
        $connections = CommonMigrationFunctions::getAllConnectionStrings();
        $manager = CommonMigrationFunctions::getGlobalDbConnectionManger();
        $doctrineImbullDbConnection = CommonMigrationFunctions::getGlobalDbConnection($connections);

        foreach ($connections as $key => $connection) {
            if ($key != 'imbull') {
                try {
                    $this->send($connection['dsn'], $key, $connections['imbull']);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }
        $manager->closeConnection($doctrineImbullDbConnection);
    }

    protected function send($dsn, $key, $imbull)
    {
        if ($key == 'en') {
            $this->localePath = '';
            $this->hostName = "http://www.kortingscode.nl";
            $this->logo = $this->hostName ."/public/images/HeaderMail.gif" ;
            $suffix = "" ;
            $this->locale = '';
            $this->siteName = "Kortingscode.nl";
            $this->publicCdnPath = "http://img.kortingscode.nl/public/";
        } else {
            $this->localePath = $key . "/";
            $this->hostName = "http://www.flipit.com";
            $suffix = "_" . strtoupper($key) ;
            $this->locale = $key;
            $this->siteName = "Flipit.com";
            $this->logo =  $this->hostName ."/public/images/flipit-welcome-mail.jpg";
            $this->publicCdnPath = "http://img.flipit.com/public/" . strtolower($this->localePath);
        }

        $doctrineSiteConnection = Doctrine_Manager::connection($dsn, 'doctrine_site');
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));
        $manager = Doctrine_Manager::getInstance();
        $manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
        $manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        Doctrine_Core::loadModels(APPLICATION_PATH . '/models');
        try {
            $codeAlertSettings = CodeAlertSettings::getCodeAlertSettings();
            $localeSettings = LocaleSettings::getLocaleSettings();
            $cutsomLocale = !empty( $localeSettings[0]['locale']) ? $localeSettings[0]['locale'] : 'nl_NL';
            $this->zendTranslate = new Zend_Translate(array(
                    'adapter' => 'gettext',
                    'disableNotices' => true));
            $this->zendTranslate->addTranslation(
                array(
                    'content' => APPLICATION_PATH.'/../public/'. strtolower($this->localePath).
                    'language/frontend_php' . $suffix . '.mo',
                    'locale' => $cutsomLocale,
                )
            );
            $this->zendTranslate->addTranslation(
                array(
                    'content' => APPLICATION_PATH.'/../public/'. strtolower($this->localePath).
                    'language/email' . $suffix . '.mo',
                    'locale' => $cutsomLocale
                )
            );
            $this->zendTranslate->addTranslation(
                array(
                    'content' => APPLICATION_PATH.'/../public/'. strtolower($this->localePath).
                    'language/form' . $suffix . '.mo',
                    'locale' => $cutsomLocale
                )
            );
            $this->zendTranslate->addTranslation(
                array(
                    'content'   => APPLICATION_PATH.'/../public/'. strtolower($this->localePath).
                    'language/po_links' . $suffix . '.mo',
                    'locale'    => $cutsomLocale
                )
            );
            Zend_Registry::set('Zend_Translate', $this->zendTranslate);
            Zend_Registry::set('Zend_Locale', $cutsomLocale);
            echo "\nSending code alert...\n" ;
            $this->mandrilHandler($key, $codeAlertSettings);
        } catch (Exception $e) {
            echo "\n";
        }

        $manager->closeConnection($doctrineSiteConnection);
    }

    protected function setPhpExecutionLimit()
    {
        set_time_limit(10000);
        ini_set('max_execution_time', 115200);
        ini_set("memory_limit", "1024M");
    }

    protected function mandrilHandler($key, $settings)
    {
        $message = '';
        $this->_linkPath = $this->hostName . '/' .$this->localePath;
        $codeAlertOffers = CodeAlertQueue::getCodealertOffers();
        if (!empty($codeAlertOffers)) {
            foreach ($codeAlertOffers as $codeAlertOffer) {
                $currentDate = date('Y-m-d H:i:s');
                if ($codeAlertOffer['endDate'] < $currentDate) {
                    CodeAlertQueue::moveCodeAlertToTrash($codeAlertOffer['id']);
                }
                if (($codeAlertOffer['startDate'] <= $currentDate && $codeAlertOffer['endDate'] >= $currentDate) && $codeAlertOffer['offline'] == 0) {
                    $this->setPhpExecutionLimit();
                    $topVouchercodes = FrontEnd_Helper_viewHelper::getShopCouponCode(
                        'similarStoresAndSimilarCategoriesOffers',
                        4,
                        $codeAlertOffer['shop']['id']
                    );
                    $codeAlertSettings = CodeAlertSettings::getCodeAlertSettings();
                    $settings = Signupmaxaccount::getAllMaxAccounts();
                    $mandrillSenderEmailAddress = $settings[0]['emailperlocale'];
                    $mandrillNewsletterSubject = isset($codeAlertSettings[0]['email_subject'])
                        && $codeAlertSettings[0]['email_subject'] != ''
                        ? $codeAlertSettings[0]['email_subject']
                        : '';
                    $mandrillSenderName = $settings[0]['sendername'];
                    $visitors = $codeAlertOffer['shop']['visitors'];
                    $visitorIds = array();
                    foreach ($visitors as $visitorInfo) {
                        $codeAlertVisitors = CodeAlertVisitors::getVisitorsToRemoveInCodeAlert(
                            $visitorInfo['visitorId'],
                            $codeAlertOffer['id']
                        );
                        if (empty($codeAlertVisitors)) {
                            $visitorCodeAlertSendDate = Shop::getCodeAlertSendDateByShopId($codeAlertOffer['shop']['id']);
                            if (date('Y-m-d', strtotime($visitorCodeAlertSendDate)) == date('Y-m-d')) {
                            } else {
                                $visitorIds[] = $visitorInfo['visitorId'];
                            }
                        }
                    }
                    if (!empty($visitorIds)) {
                        $visitorIds = implode(',', $visitorIds);
                        $this->visitorId = $visitorIds;
                        $this->shopId = $codeAlertOffer['shop']['id'];
                        BackEnd_Helper_MandrillHelper::getDirectLoginLinks(
                            $this,
                            'scheduleNewsletterSender',
                            '',
                            $this->mandrillKey
                        );
                        try {
                            $codeAlertHeader = isset($codeAlertSettings[0]['email_header'])
                                ? $codeAlertSettings[0]['email_header']
                                : 'Code alert header';
                            FrontEnd_Helper_viewHelper::sendMandrillNewsletterByBatch(
                                '',
                                '',
                                '',
                                str_replace('[shopname]', $codeAlertOffer['shop']['name'], $mandrillNewsletterSubject),
                                $mandrillSenderEmailAddress,
                                $mandrillSenderName,
                                $this->_recipientMetaData,
                                $this->_loginLinkAndData,
                                $this->_to,
                                '',
                                array(
                                    'httpPath' => $this->hostName,
                                    'locale' => $this->locale,
                                    'httpPathLocale' => $this->_linkPath,
                                    'publicPathCdn' => $this->publicCdnPath,
                                    'mandrillKey' => $this->mandrillKey
                                ),
                                str_replace('[shopname]', $codeAlertOffer['shop']['name'], $codeAlertHeader),
                                $codeAlertOffer
                            );
                            Shop::addCodeAlertTimeStampForShopId($codeAlertOffer['shop']['id']);
                            CodeAlertVisitors::saveCodeAlertVisitors($visitorIds, $codeAlertOffer['id']);
                            CodeAlertQueue::clearCodeAlertQueueByOfferId($codeAlertOffer['id']);
                            $message = 'code alert has been sent successfully' ;
                        } catch (Mandrill_Error $e) {
                            $message ='There is some problem in your data';
                        }
                    } else {
                        $message ='Code alert already sent...';
                    }
                } else {
                    $message .=' and code alert cannot be sent for other offers yet to start or expired.';
                }
            }
        } else {
            $message ='Code alert queue empty.';
        }
        echo "\n";
        print "$key - $message ";
    }
}

new SendCodeAlert();
