<?php
class BootstrapDoctrineConnectionFunctions {

    public static function doctrineConnection($doctrineOptions, $moduleDirectoryName, $localeCookieData)
    {
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));
        $manager = Doctrine_Manager::getInstance();
        $manager->setAttribute(
            Doctrine_Core::ATTR_MODEL_LOADING,
            Doctrine_Core::MODEL_LOADING_CONSERVATIVE
        );
        $manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        Doctrine_Core::loadModels(APPLICATION_PATH . '/models');
        $doctrineOptions = $doctrineOptions;
        $imbullDbConnection = Doctrine_Manager::connection(
            $doctrineOptions['imbull'],
            'doctrine'
        );

        $localSiteDbConnection = self::getLocaleNameForDbConnection($moduleDirectoryName, $localeCookieData);
        Doctrine_Manager::connection(
            $doctrineOptions[strtolower($localSiteDbConnection)]['dsn'],
            'doctrine_site'
        );
         $locale = LocaleSettings::getLocaleSettings();
        $localeValue = explode('_', $locale[0]['locale']);
        if (LOCALE == '') {
            date_default_timezone_set('Europe/Amsterdam');
        } else if (strtolower($localeValue[1]) == LOCALE) {
            date_default_timezone_set($locale[0]['timezone']);
        }
        return $imbullDbConnection;
    }

    public static function getLocaleNameForDbConnection($moduleDirectoryName, $localeCookieData)
    {
        $locale = 'en';
        $httpScheme = FrontEnd_Helper_viewHelper::getServerNameScheme();
        if (strlen($moduleDirectoryName) == 2 && HTTP_HOST== $httpScheme.'.flipit.com') {
            $locale = $moduleDirectoryName;
        } elseif ($moduleDirectoryName == 'admin') {
            $locale = isset($localeCookieData) ? $localeCookieData : 'en';
        } elseif ($moduleDirectoryName == "default") {
            $locale = 'en';
        } elseif (strlen($moduleDirectoryName) == 2 && HTTP_HOST=='www.kortingscode.nl') {
            $locale = 'en';
        }
        return $locale;
    }
}