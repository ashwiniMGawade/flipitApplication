<?php
class BootstrapTranslationFunctions
{
    public static function getTranslationSettings($domain, $moduleDirectoryName, $localeCookieData)
    {
        # add suffix according to locale
        $suffix = "";
        if (LOCALE) {
            $suffix = "_" . strtoupper(LOCALE);
        }

        if (strlen($moduleDirectoryName) == 2) {
            if ($domain != "www.kortingscode.nl" && $domain != "kortingscode.nl") {
                $localePath = '/'.$moduleDirectoryName.'/';
            } else {
                $localePath = '/';
            }
        } elseif ($moduleDirectoryName == 'admin') {
            $localePath = isset($localeCookieData) && $localeCookieData != 'en'
                ? '/'.$localeCookieData.'/' : '/';
        } else {
            $localePath = '/';
        }
        return array('locale' => COUNTRY_LOCALE, 'localePath' => $localePath, 'suffix' => $suffix);
    }

    public static function setTranslationInZendRegistery($domain, $moduleDirectoryName, $localeCookieData)
    {
        $transSettings = self::getTranslationSettings($domain, $moduleDirectoryName, $localeCookieData);
        $locale = $transSettings['locale'];
        \Zend_Registry::set('Zend_Locale', $locale);
        return;
    }

    public static function setDateConstantsForLocale()
    {
        $date = new Zend_Date();
        $month = $date->get(Zend_Date::MONTH_NAME);
        $year = $date->get(Zend_Date::YEAR);
        $day = $date->get(Zend_Date::DAY);
        defined('CURRENT_MONTH') || define('CURRENT_MONTH', $month);
        defined('CURRENT_YEAR') || define('CURRENT_YEAR', $year);
        defined('CURRENT_DAY') || define('CURRENT_DAY', $day);
    }

    public static function activateInlineTranslationForAdmin($domain, $moduleDirectoryName, $localeCookieData)
    {
        $transSettings = self::getTranslationSettings($domain, $moduleDirectoryName, $localeCookieData);
        if ($moduleDirectoryName != 'admin') {
            $session        = new \Zend_Session_Namespace('Transl8');
            $activationMode = (isset($session->onlineTranslationActivated))
            ? $session->onlineTranslationActivated
            : false;
        } else {
            $activationMode = false;
        }

        \Zend_Registry::set('Transl8_Activated', $activationMode);
        \Transl8_Translate_Writer_Csv::setDestinationFolder(
            APPLICATION_PATH.'/../public'.$transSettings['localePath'].'language'
        );
        
        if (\Zend_Registry::get('Transl8_Activated')) {
            $plugin = new \Transl8_Controller_Plugin_Transl8();
            $plugin->setActionGetFormData($transSettings['localePath'].'trans/getformdata');
            $plugin->setActionSubmit($transSettings['localePath'].'trans/submit');
            $front = \Zend_Controller_Front::getInstance();
            $front->registerPlugin($plugin);

            \Zend_Controller_Action_HelperBroker::addPath(
                APPLICATION_PATH . '/../library/Transl8/Controller/Action/Helper/',
                'Transl8_Controller_Action_Helper'
            );
            $locales = '';
            $locales[\Zend_Registry::get('Zend_Locale')] = \Zend_Registry::get('Zend_Locale');
            \Transl8_Form::setLocales($locales);
        }
    }

    public static function setTranslationFilesForLocale($domain, $moduleDirectoryName, $localeCookieData)
    {
        $transSettings = self::getTranslationSettings($domain, $moduleDirectoryName, $localeCookieData);
        $locale        = $transSettings['locale'];

        \Zend_Locale::setDefault('en_US');
        $locale = new \Zend_Locale(Zend_Registry::get('Zend_Locale'));
        $poTrans = new \Zend_Translate(array('adapter' => 'gettext', 'locale'  => $locale, 'disableNotices' => true));
        self::addTranslationFileInRegistry($poTrans, $transSettings, 'language/fallback/frontend_php', $locale);
        self::addTranslationFileInRegistry($poTrans, $transSettings, 'language/backend_php', $locale);
        self::getSavedTranslationFileAndSetInRegistry($poTrans, $locale);
        self::addTranslationFileInRegistry($poTrans, $transSettings, 'language/email', $locale);
        self::addTranslationFileInRegistry($poTrans, $transSettings, 'language/form', $locale);
        self::addTranslationFileInRegistry($poTrans, $transSettings, 'language/po_links', $locale);
        \Zend_Registry::set('Zend_Locale', $locale);
        \Zend_Registry::set('Zend_Translate', $poTrans);
    }

    public static function addTranslationFileInRegistry($poTrans, $transSettings, $fileName, $locale)
    {
        $poTrans->addTranslation(
            array(
                'content'   => APPLICATION_PATH.'/../public'.strtolower($transSettings['localePath'])
                . $fileName . $transSettings['suffix'] . '.mo',
                'locale'    => $locale
            )
        );
        return;
    }

    public static function getSavedTranslationFileAndSetInRegistry($poTrans, $locale)
    {
        $translateSession = new \Zend_Session_Namespace('Transl8');
        if (!empty($translateSession->onlineTranslationActivated)) {
            $dbTranslations = self::getDbTranslations($locale);
            $poTrans->addTranslation($dbTranslations);
        } else {
            $csvTranslate = self::getCsvTranslations($locale);
            $poTrans->addTranslation($csvTranslate);
        }
        return;
    }

    public static function getDbTranslations($locale)
    {
        $getDbTranslationsForZendTranslate = \KC\Repository\Translations::getDbTranslationsForZendTranslate();
        
        $dbTranslations = new \Zend_Translate(
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

    public static function getCsvTranslations($locale)
    {
        $inlineTranslationFolder = \Transl8_Translate_Writer_Csv::getDestinationFolder();
        $csvTranslation = array(
            'adapter'   => 'Transl8_Translate_Adapter_Csv',
            'scan'      => \Zend_Translate::LOCALE_DIRECTORY,
            'content'   => $inlineTranslationFolder . '/',
            'locale'    => $locale
        );
        $csvTranslate = new \Zend_Translate($csvTranslation);
        return $csvTranslate;
    }
}
