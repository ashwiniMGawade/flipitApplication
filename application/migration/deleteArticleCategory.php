<?php

/**
 * Script for Deleting all article category from atriclecategory table
 */
class deleteArticleCategory
{
    protected $_localePath = '/';
    protected $_hostName = '';
    protected $_translate = null;

    public function __construct()
    {
        require_once('ConstantForMigration.php');
        require_once('databaseConnectionForMigrations.php');
        foreach ($databaseConnections as $databaseConnectionKey => $databaseConnection ) {
            if ($databaseConnectionKey != 'imbull') {
                try {
                    $this->deleteArticleCategories($databaseConnection ['dsn'], $databaseConnectionKey, $imbull);
                } catch (Exception $e) {
                    echo $e->getMessage ();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }
        $doctrineManager->closeConnection($doctrineManagerConnection);
    }

    protected function deleteArticleCategories($dsn, $localeKey, $imbull)
    {
        if ($localeKey == 'en') {
            $this->_localePath = '';
            $this->_hostName = "http://www.kortingscode.nl";
            $this->_logo = $this->_hostName . "/public/images/front_end/logo-popup.png";
            $locale = "" ;
        } else {
            $this->_localePath = $localeKey . "/";
            $this->_hostName = "http://www.flipit.com";
            $locale = "_" . strtoupper($localeKey) ;
        }

        $doctrineManagerConnection = Doctrine_Manager::connection($dsn, 'doctrine_site');
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));
        $doctrineManager = Doctrine_Manager::getInstance();
        $doctrineManager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
        $doctrineManager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $doctrineManager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        Doctrine_Core::loadModels(APPLICATION_PATH . '/models');
        $databaseLocale = LocaleSettings::getLocaleSettings();
        $databaseLocale = !empty($databaseLocale[0]['locale']) ? $databaseLocale[0]['locale'] : 'nl_NL';
        $maxAccountTableValues = LocaleSettings::getLocaleSettings();
        $currentLocale = !empty($maxAccountTableValues[0]['locale']) ? $maxAccountTableValues[0]['locale'] : 'nl_NL';

        $this->_translate = new Zend_Translate(array(
                'adapter' => 'gettext',
                'disableNotices' => true));
        $this->_translate->addTranslation(
                array(
                        'content' => APPLICATION_PATH.'/../public/'. strtolower($this->_localePath).'language/frontend_php' . $locale . '.mo',
                        'locale' => $databaseLocale,
                )
        );
        $this->_translate->addTranslation(
                array(
                        'content' => APPLICATION_PATH.'/../public/'.strtolower($this->_localePath).'language/po_links' . $locale . '.mo',
                        'locale' => $databaseLocale ,
                )
        );

        $currentDate = new Zend_Date();
        $currentMonth = $currentDate->get(Zend_Date::MONTH_NAME);
        $currentYear = $currentDate->get(Zend_Date::YEAR);
        $currentDay = $currentDate->get(Zend_Date::DAY);

        defined('CURRENT_MONTH')
                || define('CURRENT_MONTH', $currentMonth);

        defined('CURRENT_YEAR')
        || define('CURRENT_YEAR', $currentYear);

        defined('CURRENT_DAY')
        || define('CURRENT_DAY', $currentDay);

        $deletedRelatedArticleCategories =  Articlecategory::deleteAllArticleCategoriesAndReferenceArticleCategories();

        if ($deletedRelatedArticleCategories) {
            echo "\n";
            print "$localeKey - Articles have been deleted successfully!!!";
        } else {
            echo "\n";
            print "$localeKey - Articles have not been deleted!!!";
        }
        $doctrineManager->closeConnection($doctrineManagerConnection);
    }
}
new DeleteArticleCategory();
