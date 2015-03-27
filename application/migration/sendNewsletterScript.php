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
    public $_public_cdn_path = null;
    public $_recipientMetaData  = array();
    public $_loginLinkAndData = array();
    public $_to = array();
    public $_mandrillKey = "";
    public $_template = "" ;
    public $_suffix = '';

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
        $this->_mandrillKey = $frontControlerObject['frontController']['params']['mandrillKey'];
        $this->_template = $frontControlerObject['frontController']['params']['newsletterTemplate'];
        $dbConnections = CommonMigrationFunctions::getAllConnectionStrings();
        $manager = CommonMigrationFunctions::getGlobalDbConnectionManger();
        $doctrineImbullDbConnection = CommonMigrationFunctions::getGlobalDbConnection($dbConnections);
        foreach ($dbConnections as $key => $dbConnection) {
            if ($key != 'imbull') {
                try {
                    $this->send($dbConnection ['dsn'], $key);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }
        $manager->closeConnection($doctrineImbullDbConnection);
    }

    protected function send($dsn, $key)
    {
        if ($key == 'en') {
            self::setVariablesForKortingscodeSite();
        } else {
            self::setVariablesForLocales($key);
        }
        $doctrineSiteDbConnection = CommonMigrationFunctions::getDoctrineSiteConnection($dsn);
        $manager = CommonMigrationFunctions::loadDoctrineModels();
        try {
            $newsLetterSettings = Signupmaxaccount::getAllMaxAccounts();
            $localeSettings = LocaleSettings::getLocaleSettings();
            $currentDate = FrontEnd_Helper_viewHelper::getCurrentDate();
            $scheduledTime =  date('Y-m-d', strtotime($newsLetterSettings[0]['newletter_scheduled_time']));
            $newsletterSentTime = date('Y-m-d', strtotime($newsLetterSettings[0]['newsletter_sent_time']));
            if ($newsLetterSettings[0]['newletter_is_scheduled'] == 1) {
                if ($newsLetterSettings[0]['newletter_is_scheduled'] == 1
                        && $scheduledTime <= $currentDate
                        && $newsletterSentTime < $currentDate) {
                    $customLocale= !empty($localeSettings[0]['locale']) ? $localeSettings[0]['locale'] : 'nl_NL';
                    $this->_trans = new Zend_Translate(array(
                            'adapter' => 'gettext',
                            'disableNotices' => true));
                    $this->_trans->addTranslation(
                        array(
                            'content' => APPLICATION_PATH.'/../public/'. strtolower($this->_localePath).
                            'language/frontend_php' .$this->_suffix. '.mo',
                            'locale' => $customLocale,
                        )
                    );
                    $this->_trans->addTranslation(
                        array(
                            'content' => APPLICATION_PATH.'/../public/'. strtolower($this->_localePath).
                            'language/email' .$this->_suffix. '.mo',
                            'locale' => $customLocale
                        )
                    );
                    $this->_trans->addTranslation(
                        array(
                            'content' => APPLICATION_PATH.'/../public/'. strtolower($this->_localePath).
                            'language/form' .$this->_suffix. '.mo',
                            'locale' => $customLocale
                        )
                    );
                    $this->_trans->addTranslation(
                        array(
                            'content'   => APPLICATION_PATH.'/../public/'. strtolower($this->_localePath).
                            'language/po_links' .$this->_suffix. '.mo',
                            'locale'    => $customLocale
                        )
                    );
                    Zend_Registry::set('Zend_Translate', $this->_trans);
                    Zend_Registry::set('Zend_Locale', $customLocale);
                    $localeTimezone = $localeSettings[0]['timezone'];
                    echo "\n" ;
                    $newsletterScheduledDateTime = new Zend_Date($newsLetterSettings[0]['newletter_scheduled_time']);
                    $newsletterScheduledDateTime->get('YYYY-MM-dd HH:mm:ss');
                    $currentDateTime = new Zend_Date();
                    $currentDateTime->setTimezone($localeTimezone);
                    echo "\n" ;
                    $currentDateTime->get('YYYY-MM-dd HH:mm:ss');
                    if ($currentDateTime->isLater($newsletterScheduledDateTime)) {
                        echo "\nSending newletter...\n" ;
                        $this->mandrilHandler($key, $newsLetterSettings);
                    } else {
                        echo "\n";
                        print "$key - Newsletter scheduled date is greater than Current Date.";
                    }
                } else {
                    echo "\n";
                    print "$key - Newsletter has already been sent for the same day.";
                }
            } else {
                echo "\n";
                print "$key - Newsletter has not been scheduled.";
            }
        } catch (Exception $e) {
            echo "\n";
            echo $e->getMessage();
        }
        $manager->closeConnection($doctrineSiteDbConnection);
    }

    protected function setVariablesForLocales($key)
    {
        $this->_localePath = $key . "/";
        $this->_hostName = "http://www.flipit.com";
        $this->_suffix= "_" . strtoupper($key) ;
        $this->_locale = $key;
        $this->_siteName = "Flipit.com";
        $this->_logo =  $this->_hostName ."/public/images/flipit-welcome-mail.jpg";
        $this->_public_cdn_path = "http://img.flipit.com/public/" . strtolower($this->_localePath);
        return true;
    }

    protected function setVariablesForKortingscodeSite()
    {
        $this->_localePath = '';
        $this->_hostName = "http://www.kortingscode.nl";
        $this->_logo = $this->_hostName ."/public/images/HeaderMail.gif" ;
        $this->_suffix= "" ;
        $this->_locale = '';
        $this->_siteName = "Kortingscode.nl";
        $this->_public_cdn_path = "http://img.kortingscode.nl/public/";
        return true;
    }

    protected function mandrilHandler($key, $newsLetterSetings)
    {
        $this->_linkPath = $this->_hostName . '/' .$this->_localePath;
        $newsLetterCache = NewsLetterCache::getAllNewsLetterCacheContent();
        if (!empty($newsLetterCache)) {
            echo 'Building newsletter from cache'."\n";
            $topCategory = NewsLetterCache::getCategoryByFallBack($newsLetterCache['top_category_id']);
            $topVouchercodes = NewsLetterCache::getTopOffersByFallBack($newsLetterCache['top_offers_ids']);
            $categoryVouchers = NewsLetterCache::getTopCategoryOffersByFallBack(
                $newsLetterCache['top_category_offers_ids'],
                $topCategory[0]['id']
            );
            $emailHeader = NewsLetterCache::getEmailHeaderByFallBack(
                $newsLetterCache['email_header'],
                $newsLetterSetings[0]['email_header']
            );
            $emailFooter = NewsLetterCache::getEmailFooterByFallBack(
                $newsLetterCache['email_footer'],
                $newsLetterSetings[0]['email_footer']
            );
            $categoryName = $topCategory[0]['name'];
            $categoryPermalink = $topCategory[0]['permaLink'];
        } else {
            echo 'Building newsletter not from cache'."\n";
            $topCategory = array_slice(FrontEnd_Helper_viewHelper::gethomeSections("category", 10), 0, 1);
            $topVouchercodes = Offer::getTopOffers(10);
            $categoryVouchers = array_slice(Category::getCategoryVoucherCodes($topCategory[0]['categoryId']), 0, 3);
            $emailHeader = $newsLetterSetings[0]['email_header'];
            $emailFooter = $newsLetterSetings[0]['email_footer'];
            $categoryName = $topCategory[0]['category']['name'];
            $categoryPermalink = $topCategory[0]['category']['permaLink'];
        }
 
        BackEnd_Helper_MandrillHelper::getDirectLoginLinks($this, 'scheduleNewsletterSender', '', $this->_mandrillKey);
        $mandrill = new Mandrill_Init($this->_mandrillKey);
        $mandrillSenderEmailAddress = $newsLetterSetings[0]['emailperlocale'];
        $mandrillNewsletterSubject = $newsLetterSetings[0]['emailsubject'];
        $mandrillSenderName = $newsLetterSetings[0]['sendername'];
        $newsLetterHeaderImage = Newsletterbanners::getHeaderOrFooterImage('header');
        $newsLetterHeaderImage = !empty($newsLetterHeaderImage) ? $newsLetterHeaderImage : '';
        $newsLetterFooterImage = Newsletterbanners::getHeaderOrFooterImage('footer');
        $newsLetterFooterImage = !empty($newsLetterFooterImage) ? $newsLetterFooterImage : '';
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
                $emailFooter,
                array(
                    'httpPath' => $this->_hostName,
                    'locale' => $this->_locale,
                    'httpPathLocale' => $this->_linkPath,
                    'publicPathCdn' => $this->_public_cdn_path,
                    'mandrillKey' => $this->_mandrillKey
                ),
                $emailHeader,
                '',
                $newsLetterHeaderImage,
                $newsLetterFooterImage
            );
            Signupmaxaccount::updateNewsletterSchedulingStatus();
            NewsLetterCache::truncateNewsletterCacheTable();
            $message = 'Newsletter has been sent successfully';
        } catch (Mandrill_Error $e) {
            $message ='There is some problem in your data';
        }
        echo "\n";
        print "$key - $message ";
    }
}
new SendNewsletter();
